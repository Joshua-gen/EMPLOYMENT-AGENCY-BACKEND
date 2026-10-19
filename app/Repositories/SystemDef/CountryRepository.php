<?php

namespace App\Repositories\SystemDef;

use App\Helpers\PaginationHelper;
use App\Models\SystemDef\Country;

class CountryRepository
{
    // Lahat ng countries (walang pagination) -- para sa select / dropdown
    public function all(array $filters)
    {
        return $this->query($filters)->get(['id', 'name', 'code']);
    }

    public function paginate(array $filters)
    {
        return $this->query($filters)->paginate(PaginationHelper::perPage($filters['per_page'] ?? null));
    }

    public function find(int $id)
    {
        return Country::find($id);
    }

    public function create(array $data)
    {
        return Country::create($data);
    }

    public function update(Country $country, array $data)
    {
        $country->update($data);

        return $country->fresh();
    }

    public function delete(Country $country): bool
    {
        return $country->delete();
    }

    private function query(array $filters)
    {
        $search = trim($filters['search'] ?? '');

        return Country::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->orderBy('name');
    }
}
