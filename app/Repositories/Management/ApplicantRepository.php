<?php

namespace App\Repositories\Management;

use App\Helpers\PaginationHelper;
use App\Models\Management\Applicant;
use App\Models\Management\ApplicantDocument;

class ApplicantRepository
{
    public function paginate(array $filters)
    {
        $search = trim($filters['search'] ?? '');
        $status = $filters['status'] ?? 'All';
        $source = $filters['source'] ?? 'All';
        $channel = $filters['channel'] ?? 'All';
        $country = $filters['country'] ?? 'All';
        $jobId = $filters['job_id'] ?? 'All';
        $unassigned = filter_var($filters['unassigned'] ?? false, FILTER_VALIDATE_BOOLEAN);

        return Applicant::query()
            ->with($this->relations())
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('reference', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhereHas('job', fn ($j) => $j->where('title', 'like', "%{$search}%"))
                        ->orWhereHas('employer', fn ($e) => $e->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($status !== 'All', fn ($query) => $query->where('status', $status))
            ->when(in_array($source, ['job', 'direct'], true), fn ($query) => $query->where('source', $source))
            ->when($channel === 'walk_in', fn ($query) => $query->where('channel', 'walk_in'))
            ->when($unassigned, fn ($query) => $query->whereNull('job_opening_id'))
            ->when($jobId !== 'All', fn ($query) => $query->where('job_opening_id', $jobId))
            ->when($country !== 'All', fn ($query) => $query->whereHas('job', fn ($j) => $j->where('country', $country)))
            ->latest('id')
            ->paginate(PaginationHelper::perPage($filters['per_page'] ?? null));
    }

    public function summary(): array
    {
        $byStatus = Applicant::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = [
            'All'        => Applicant::count(),
            'job'        => Applicant::where('source', 'job')->count(),
            'direct'     => Applicant::where('source', 'direct')->count(),
            'unassigned' => Applicant::whereNull('job_opening_id')->count(),
        ];

        foreach (Applicant::STATUSES as $status) {
            $counts[$status] = (int) ($byStatus[$status] ?? 0);
        }

        return $counts;
    }

    public function find(int $id)
    {
        return Applicant::with($this->detailRelations())->find($id);
    }

    public function findDocument(int $id)
    {
        return ApplicantDocument::find($id);
    }

    // Public apply at admin entry: tatanggap ng data at ng mga UploadedFile (key = type ng document)
    public function create(array $data, array $files = [])
    {
        $data['work_start'] = $this->monthToDate($data['work_start'] ?? null);
        $data['work_end'] = $this->monthToDate($data['work_end'] ?? null);
        $data['source'] = ! empty($data['job_opening_id']) ? 'job' : 'direct';
        $data['status'] = 'Submitted';
        $data['applied_at'] = $data['applied_at'] ?? now()->toDateString();

        $applicant = Applicant::create($data);

        $applicant->update([
            'reference' => sprintf('APP-%s-%03d', $applicant->applied_at->format('Y'), $applicant->id),
        ]);

        foreach ($files as $type => $file) {
            $applicant->documents()->create([
                'type'          => $type,
                'original_name' => $file->getClientOriginalName(),
                'path'          => $file->store("applicants/{$applicant->id}", 'local'),
                'mime'          => $file->getClientMimeType(),
                'size'          => $file->getSize(),
            ]);
        }

        return $applicant->load($this->detailRelations());
    }

    public function update(Applicant $applicant, array $data)
    {
        $applicant->update($data);

        return $applicant->fresh($this->detailRelations());
    }

    // Ang job ay may employers para sa Assign Employer modal
    private function relations(): array
    {
        return [
            'job' => fn ($query) => $query
                ->select('id', 'title', 'country', 'salary')
                ->with('employers:employers.id,employers.name'),
            'employer:id,name',
        ];
    }

    private function detailRelations(): array
    {
        return array_merge($this->relations(), ['documents']);
    }

    // "2022-03" -> "2022-03-01"
    private function monthToDate(?string $value): ?string
    {
        return $value ? $value . '-01' : null;
    }
}
