<?php

namespace App\Models\Management;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ManpowerRequest extends Model
{
    public const STATUSES = ['Pending', 'Approved', 'Rejected'];

    protected $table = 'manpower_requests';

    protected $fillable = [
        'reference',
        'status',
        'employer_id',
        'business_name',
        'business_category_id',
        'country',
        'business_address',
        'license_no',
        'contact_person',
        'contact_position',
        'contact_tel',
        'contact_email',
        'website',
        'remarks',
        'requested_at',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'requested_at'   => 'date:Y-m-d',
        'reviewed_at'    => 'datetime',
        'total_required' => 'integer',
    ];

    protected $hidden = ['reviewed_by'];

    public function businessCategory(): BelongsTo
    {
        return $this->belongsTo(BusinessCategory::class, 'business_category_id');
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class, 'employer_id');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(ManpowerRequestPosition::class, 'manpower_request_id')->orderBy('id');
    }
}
