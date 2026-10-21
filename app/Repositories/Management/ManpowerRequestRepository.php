<?php

namespace App\Repositories\Management;

use App\Helpers\PaginationHelper;
use App\Models\Management\Employer;
use App\Models\Management\JobOpening;
use App\Models\Management\ManpowerRequest;
use App\Models\Management\ManpowerRequestPosition;
use Illuminate\Support\Arr;

class ManpowerRequestRepository
{
    public function paginate(array $filters)
    {
        $search = trim($filters['search'] ?? '');
        $status = $filters['status'] ?? 'All';
        $country = $filters['country'] ?? 'All';

        return ManpowerRequest::query()
            ->with(['businessCategory', 'employer:id,name'])
            ->withCount([
                'positions',
                'positions as unlinked_positions_count' => fn ($query) => $query->whereNull('job_opening_id'),
            ])
            ->withSum('positions as total_required', 'number_required')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('reference', 'like', "%{$search}%")
                        ->orWhere('business_name', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%")
                        ->orWhere('contact_email', 'like', "%{$search}%")
                        ->orWhereHas('positions', fn ($p) => $p->where('position', 'like', "%{$search}%"));
                });
            })
            ->when(in_array($status, ManpowerRequest::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->when($country !== 'All', fn ($query) => $query->where('country', $country))
            ->latest('id')
            ->paginate(PaginationHelper::perPage($filters['per_page'] ?? null));
    }

    public function summary(): array
    {
        $byStatus = ManpowerRequest::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = ['All' => ManpowerRequest::count()];

        foreach (ManpowerRequest::STATUSES as $status) {
            $counts[$status] = (int) ($byStatus[$status] ?? 0);
        }

        // mga position ng Approved na request na wala pang job opening
        $counts['unlinked_positions'] = ManpowerRequestPosition::query()
            ->whereNull('job_opening_id')
            ->whereHas('manpowerRequest', fn ($query) => $query->where('status', 'Approved'))
            ->count();

        return $counts;
    }

    public function find(int $id)
    {
        return ManpowerRequest::query()
            ->with(['businessCategory', 'employer:id,name,country', 'positions.job:id,title,country,status'])
            ->withCount('positions')
            ->withSum('positions as total_required', 'number_required')
            ->find($id);
    }

    public function findEmployer(int $id)
    {
        return Employer::find($id);
    }

    // Parehong pangalan o parehong license no. = duplicate
    public function duplicateEmployer(ManpowerRequest $request)
    {
        return Employer::query()
            ->where('name', $request->business_name)
            ->when($request->license_no, fn ($query) => $query->orWhere('license_no', $request->license_no))
            ->first();
    }

    // Mga posibleng tugma, hint lang kay admin
    public function possibleEmployers(ManpowerRequest $request)
    {
        return Employer::query()
            ->select('id', 'name', 'country', 'license_no', 'contact_email')
            ->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->business_name . '%')
                    ->orWhere('contact_email', $request->contact_email);

                if ($request->license_no) {
                    $query->orWhere('license_no', $request->license_no);
                }
            })
            ->orderBy('name')
            ->limit(10)
            ->get();
    }

    // Public submit
    public function create(array $data)
    {
        $positions = Arr::pull($data, 'positions', []);

        $data['status'] = 'Pending';
        $data['requested_at'] = now()->toDateString();

        $request = ManpowerRequest::create($data);

        $request->update([
            'reference' => sprintf('MR-%s-%03d', $request->requested_at->format('Y'), $request->id),
        ]);

        $request->positions()->createMany($positions);

        return $request;
    }

    public function createEmployerFrom(ManpowerRequest $request): Employer
    {
        return Employer::create([
            'name'                 => $request->business_name,
            'business_category_id' => $request->business_category_id,
            'country'              => $request->country,
            'address'              => $request->business_address,
            'license_no'           => $request->license_no,
            'contact_person'       => $request->contact_person,
            'contact_position'     => $request->contact_position,
            'contact_tel'          => $request->contact_tel,
            'contact_email'        => $request->contact_email,
            'website'              => $request->website,
        ]);
    }

    public function approve(ManpowerRequest $request, Employer $employer, ?string $remarks, ?int $userId)
    {
        $request->update([
            'status'      => 'Approved',
            'employer_id' => $employer->id,
            'remarks'     => $remarks,
            'reviewed_by' => $userId,
            'reviewed_at' => now(),
        ]);

        return $this->find($request->id);
    }

    public function reject(ManpowerRequest $request, string $remarks, ?int $userId)
    {
        $request->update([
            'status'      => 'Rejected',
            'remarks'     => $remarks,
            'reviewed_by' => $userId,
            'reviewed_at' => now(),
        ]);

        return $this->find($request->id);
    }

    // Idinadagdag lang ang employer sa job. Walang vacancies.
    public function linkPosition(ManpowerRequest $request, ManpowerRequestPosition $position, JobOpening $job)
    {
        $job->employers()->syncWithoutDetaching([$request->employer_id]);

        $position->update(['job_opening_id' => $job->id]);

        return $this->find($request->id);
    }

    // Tinatanggal lang ang link ng position. Hindi tinatanggal ang employer sa job.
    public function unlinkPosition(ManpowerRequest $request, ManpowerRequestPosition $position)
    {
        $position->update(['job_opening_id' => null]);

        return $this->find($request->id);
    }
}
