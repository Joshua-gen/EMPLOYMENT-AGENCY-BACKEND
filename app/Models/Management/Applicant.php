<?php

namespace App\Models\Management;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Applicant extends Model
{
    public const STATUSES = [
        'Submitted', 'Under Review', 'Shortlisted', 'Interview',
        'Selected', 'Processing', 'Deployed', 'Rejected',
    ];

    // kailangan ng employer bago umabot dito
    public const STATUSES_REQUIRING_EMPLOYER = ['Interview', 'Selected', 'Processing', 'Deployed'];

    protected $table = 'applicants';

    protected $fillable = [
        'reference',
        'source',
        'job_opening_id',
        'employer_id',
        'name',
        'email',
        'phone',
        'birthday',
        'gender',
        'civil_status',
        'address',
        'educational_attainment',
        'school',
        'course',
        'education_start_year',
        'education_graduated_year',
        'previous_employer',
        'previous_employer_address',
        'previous_position',
        'work_start',
        'work_end',
        'skills',
        'certifications',
        'languages',
        'status',
        'remarks',
        'applied_at',
        'channel',
        'created_by',
    ];

    protected $casts = [
        'birthday'                 => 'date:Y-m-d',
        'applied_at'               => 'date:Y-m-d',
        'work_start'               => 'date:Y-m',
        'work_end'                 => 'date:Y-m',
        'education_start_year'     => 'integer',
        'education_graduated_year' => 'integer',
    ];

    protected $appends = ['years_experience'];

    // Kinakalkula mula sa work_start at work_end (o hanggang ngayon kung walang work_end)
    protected function yearsExperience(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->work_start) {
                return null;
            }

            $end = $this->work_end ?? now();
            $months = ($end->year - $this->work_start->year) * 12 + ($end->month - $this->work_start->month);

            return round(max($months, 0) / 12, 1);
        });
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class, 'job_opening_id');
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class, 'employer_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicantDocument::class, 'applicant_id');
    }
}
