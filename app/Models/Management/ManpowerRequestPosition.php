<?php

namespace App\Models\Management;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManpowerRequestPosition extends Model
{
    protected $table = 'manpower_request_positions';

    protected $fillable = [
        'manpower_request_id',
        'position',
        'number_required',
        'description',
        'job_opening_id',
        'linked_vacancies',
    ];

    protected $casts = [
        'number_required'  => 'integer',
        'linked_vacancies' => 'integer',
    ];

    protected $hidden = ['manpower_request_id', 'created_at', 'updated_at'];

    public function manpowerRequest(): BelongsTo
    {
        return $this->belongsTo(ManpowerRequest::class, 'manpower_request_id');
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class, 'job_opening_id');
    }
}
