<?php

namespace App\Http\Requests\Management;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'          => 'sometimes|required|string|max:255',
            'country'        => 'sometimes|required|string|max:255',
            'description'    => 'sometimes|required|string',
            'vacancies'      => 'sometimes|required|string|max:50',
            'salary'         => 'sometimes|nullable|string|max:255',
            'deadline'       => 'sometimes|required|date',
            'status'         => 'sometimes|required|in:Published,Draft,Closed',
            'employer_ids'   => 'sometimes|required|array|min:1',
            'employer_ids.*' => 'integer|distinct|exists:employers,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'employer_ids'   => 'employers',
            'employer_ids.*' => 'employer',
        ];
    }
}
