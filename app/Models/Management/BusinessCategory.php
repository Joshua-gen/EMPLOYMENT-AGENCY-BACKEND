<?php

namespace App\Models\Management;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessCategory extends Model
{
    protected $table = 'business_categories';

    protected $fillable = ['name'];

    protected $hidden = ['created_at', 'updated_at'];

    public function employers(): HasMany
    {
        return $this->hasMany(Employer::class, 'business_category_id');
    }
}
