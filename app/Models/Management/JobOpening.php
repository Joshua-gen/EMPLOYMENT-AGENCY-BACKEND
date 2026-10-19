<?php

namespace App\Models\Management;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobOpening extends Model
{
    protected $table = 'job_openings';

    protected $fillable = [
        'title',
        'country',
        'description',
        'vacancies',
        'salary',
        'deadline',
        'status',
    ];

    protected $casts = [
        'deadline' => 'date:Y-m-d',
    ];

    public function employers(): BelongsToMany
    {
        return $this->belongsToMany(Employer::class, 'employer_job_opening', 'job_opening_id', 'employer_id')
            ->withTimestamps()
            ->orderBy('employers.name');
    }

    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class, 'job_opening_id');
    }
}
