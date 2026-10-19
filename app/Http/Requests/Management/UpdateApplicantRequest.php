<?php

namespace App\Http\Requests\Management;

use App\Models\Management\Applicant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Lahat ay sometimes, kasi ang PATCH {status} lang ay gumagamit din nito
    public function rules(): array
    {
        return [
            'status'  => ['sometimes', 'required', Rule::in(Applicant::STATUSES)],
            'remarks' => 'sometimes|nullable|string|max:2000',
        ];
    }
}
