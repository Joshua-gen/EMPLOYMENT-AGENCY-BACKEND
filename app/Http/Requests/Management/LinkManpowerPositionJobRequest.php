<?php

namespace App\Http\Requests\Management;

use Illuminate\Foundation\Http\FormRequest;

class LinkManpowerPositionJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'job_opening_id' => 'required|integer|exists:job_openings,id',
        ];
    }

    public function attributes(): array
    {
        return ['job_opening_id' => 'job opening'];
    }
}
