<?php

namespace App\Repositories\Management;

use App\Models\Management\BusinessCategory;

class BusinessCategoryRepository
{
    public function all()
    {
        return BusinessCategory::query()->orderBy('name')->get();
    }
}
