<?php

namespace App\Http\Requests\Management;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignApplicantJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'job_opening_id' => [
                'required',
                'integer',
                Rule::exists('job_openings', 'id')->where('status', 'Published'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'job_opening_id.exists' => 'Only published job openings can be assigned.',
        ];
    }
}
