<?php

namespace App\Repositories\Management;

use App\Helpers\PaginationHelper;
use App\Models\Management\Employer;

class EmployerRepository
{
    public function paginate(array $filters)
    {
        $search = trim($filters['search'] ?? '');
        $country = $filters['country'] ?? null;
        $categoryId = $filters['business_category_id'] ?? null;

        return Employer::query()
            ->with('businessCategory')
            ->withCount(['jobOpenings', 'manpowerRequests'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%")
                        ->orWhere('contact_email', 'like', "%{$search}%")
                        ->orWhereHas('businessCategory', fn ($c) => $c->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($country && $country !== 'All', fn ($query) => $query->where('country', $country))
            ->when($categoryId && $categoryId !== 'All', fn ($query) => $query->where('business_category_id', $categoryId))
            ->orderBy('name')
            ->paginate(PaginationHelper::perPage($filters['per_page'] ?? null));
    }

    public function find(int $id)
    {
        return Employer::with(['businessCategory', 'jobOpenings'])
            ->withCount(['jobOpenings', 'manpowerRequests'])
            ->find($id);
    }

    public function create(array $data)
    {
        return Employer::create($data)->load('businessCategory');
    }

    public function update(Employer $employer, array $data)
    {
        $employer->update($data);

        return $employer->fresh('businessCategory');
    }

    public function delete(Employer $employer): bool
    {
        return $employer->delete();
    }
}
