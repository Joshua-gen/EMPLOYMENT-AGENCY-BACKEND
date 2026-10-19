<?php

namespace App\Http\Requests\Management;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'          => 'required|string|max:255',
            'country'        => 'required|string|max:255',
            'description'    => 'required|string',
            'vacancies'      => 'required|string|max:50',
            'salary'         => 'nullable|string|max:255',
            'deadline'       => 'required|date',
            'status'         => 'sometimes|in:Published,Draft,Closed',
            'employer_ids'   => 'required|array|min:1',
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
