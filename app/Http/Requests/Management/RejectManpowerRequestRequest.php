<?php

namespace App\Http\Requests\Management;

use Illuminate\Foundation\Http\FormRequest;

class RejectManpowerRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'remarks' => 'required|string|max:2000',
        ];
    }

    public function attributes(): array
    {
        return ['remarks' => 'reason'];
    }
}
