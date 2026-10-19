<?php

namespace App\Repositories\Management;

use App\Helpers\PaginationHelper;
use App\Models\Management\JobOpening;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class JobRepository
{
    public function paginate(array $filters)
    {
        $search = trim($filters['search'] ?? '');
        $status = $filters['status'] ?? 'All';
        $country = $filters['country'] ?? 'All';

        return JobOpening::query()
            ->with('employers.businessCategory')
            ->withCount('applicants')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('country', 'like', "%{$search}%")
                        ->orWhereHas('employers', fn ($e) => $e->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($status !== 'All', fn ($query) => $query->where('status', $status))
            ->when($country !== 'All', fn ($query) => $query->where('country', $country))
            ->latest('id')
            ->paginate(PaginationHelper::perPage($filters['per_page'] ?? null));
    }

    public function summary(): array
    {
        return [
            'published' => JobOpening::where('status', 'Published')->count(),
            'draft'     => JobOpening::where('status', 'Draft')->count(),
            'closed'    => JobOpening::where('status', 'Closed')->count(),
        ];
    }

    public function find(int $id)
    {
        return JobOpening::with('employers.businessCategory')->withCount('applicants')->find($id);
    }

    // Mga employer na tatanggalin sa job pero may naka-assign pang applicants
    public function employersInUse(JobOpening $job, array $employerIds): Collection
    {
        return $job->applicants()
            ->whereNotNull('employer_id')
            ->whereNotIn('employer_id', $employerIds)
            ->with('employer:id,name')
            ->get()
            ->pluck('employer.name')
            ->unique()
            ->values();
    }

    public function create(array $data)
    {
        $employerIds = Arr::pull($data, 'employer_ids', []);

        $job = JobOpening::create($data);
        $job->employers()->sync($employerIds);

        return $job->load('employers.businessCategory')->loadCount('applicants');
    }

    public function update(JobOpening $job, array $data)
    {
        // PATCH na status lang ang laman ay hindi dapat gumalaw sa employers
        $hasEmployers = array_key_exists('employer_ids', $data);
        $employerIds = Arr::pull($data, 'employer_ids', []);

        $job->update($data);

        if ($hasEmployers) {
            $job->employers()->sync($employerIds);
        }

        return $job->fresh('employers.businessCategory')->loadCount('applicants');
    }

    public function delete(JobOpening $job): bool
    {
        return $job->delete();
    }
}
