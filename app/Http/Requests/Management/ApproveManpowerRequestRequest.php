<?php

namespace App\Http\Requests\Management;

use Illuminate\Foundation\Http\FormRequest;

class ApproveManpowerRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employer_id'     => 'nullable|integer|exists:employers,id',
            'create_employer' => 'nullable|boolean',
            'remarks'         => 'nullable|string|max:2000',
        ];
    }

    // Isa lang: existing employer O bagong employer
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (filled($this->input('employer_id')) === $this->boolean('create_employer')) {
                $validator->errors()->add(
                    'employer_id',
                    'Select an existing employer, or choose to create a new one (not both).'
                );
            }
        });
    }
}
