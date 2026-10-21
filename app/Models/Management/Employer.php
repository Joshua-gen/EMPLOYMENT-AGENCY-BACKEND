<?php

namespace App\Models\Management;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employer extends Model
{
    protected $table = 'employers';

    protected $fillable = [
        'name',
        'business_category_id',
        'country',
        'address',
        'license_no',
        'contact_person',
        'contact_position',
        'contact_tel',
        'contact_email',
        'website',
    ];

    protected $hidden = ['pivot', 'created_at', 'updated_at'];

    public function businessCategory(): BelongsTo
    {
        return $this->belongsTo(BusinessCategory::class, 'business_category_id');
    }

    public function jobOpenings(): BelongsToMany
    {
        return $this->belongsToMany(JobOpening::class, 'employer_job_opening', 'employer_id', 'job_opening_id')
            ->withTimestamps();
    }

    public function manpowerRequests(): HasMany
    {
        return $this->hasMany(ManpowerRequest::class, 'employer_id');
    }
}
