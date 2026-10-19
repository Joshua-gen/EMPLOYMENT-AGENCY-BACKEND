<?php

namespace App\Http\Requests\Management;

use Illuminate\Foundation\Http\FormRequest;

class AssignApplicantEmployerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employer_id' => 'required|integer|exists:employers,id',
        ];
    }
}
