<?php

namespace App\Repositories\Website;

use App\Helpers\PaginationHelper;
use App\Models\Management\JobOpening;

class JobRepository
{
    private const COLUMNS = ['id', 'title', 'country', 'description', 'vacancies', 'salary', 'deadline'];

    // Published at hindi pa lampas ang deadline
    private function open()
    {
        return JobOpening::query()
            ->where('status', 'Published')
            ->whereDate('deadline', '>=', today());
    }

    public function paginate(array $filters)
    {
        $search = trim($filters['search'] ?? '');
        $country = $filters['country'] ?? 'All';

        return $this->open()
            ->select(self::COLUMNS)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('country', 'like', "%{$search}%");
                });
            })
            ->when($country !== 'All', fn ($query) => $query->where('country', $country))
            ->latest('id')
            ->paginate(PaginationHelper::perPage($filters['per_page'] ?? null));
    }

    // Mga bansang may bukas na job (para sa country filter ng website)
    public function countries(): array
    {
        return $this->open()->distinct()->orderBy('country')->pluck('country')->all();
    }

    public function find(int $id)
    {
        return $this->open()->select(self::COLUMNS)->find($id);
    }
}
