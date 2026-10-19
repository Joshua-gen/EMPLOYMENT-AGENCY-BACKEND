<?php

namespace App\Http\Requests\Country;

use Illuminate\Foundation\Http\FormRequest;

class StoreCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge(['code' => strtoupper((string) $this->input('code'))]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:countries,name',
            'code' => 'required|string|size:2|unique:countries,code',
        ];
    }
}
