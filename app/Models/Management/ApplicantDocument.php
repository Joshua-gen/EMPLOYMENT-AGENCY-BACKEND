<?php

namespace App\Models\Management;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;

class ApplicantDocument extends Model
{
    public const LABELS = [
        'resume'   => 'Resume',
        'valid_id' => 'Valid ID',
        'passport' => 'Passport',
        'other'    => 'Other Requirements',
    ];

    protected $table = 'applicant_documents';

    protected $fillable = ['applicant_id', 'type', 'original_name', 'path', 'mime', 'size'];

    protected $hidden = ['applicant_id', 'original_name', 'path', 'created_at', 'updated_at'];

    protected $appends = ['label', 'name', 'url'];

    public function getLabelAttribute(): string
    {
        return self::LABELS[$this->type] ?? $this->type;
    }

    public function getNameAttribute(): string
    {
        return $this->original_name;
    }

    // Signed link na nag-e-expire sa 30 minuto, para mabuksan sa browser nang walang Bearer token
    public function getUrlAttribute(): string
    {
        return URL::temporarySignedRoute('applicant-documents.show', now()->addMinutes(30), ['document' => $this->id]);
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }
}
