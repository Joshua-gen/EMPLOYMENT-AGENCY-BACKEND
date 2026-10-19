<?php

namespace App\Http\Requests\Management;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployerRequest extends FormRequest
{
    private const TRIMMED = [
        'name', 'country', 'address', 'license_no',
        'contact_person', 'contact_position', 'contact_tel', 'contact_email', 'website',
    ];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (self::TRIMMED as $field) {
            if ($this->has($field)) {
                $value = trim((string) $this->input($field));
                $data[$field] = $value === '' ? null : $value;
            }
        }

        // "example.com" -> "https://example.com"
        if (! empty($data['website']) && ! preg_match('#^https?://#i', $data['website'])) {
            $data['website'] = 'https://' . $data['website'];
        }

        $this->merge($data);
    }

    public function rules(): array
    {
        return [
            'name'                 => 'required|string|max:255|unique:employers,name',
            'business_category_id' => 'required|integer|exists:business_categories,id',
            'country'              => 'required|string|max:255',
            'address'              => 'required|string|max:500',
            'license_no'           => 'nullable|string|max:100|unique:employers,license_no',
            'contact_person'       => 'required|string|max:255',
            'contact_position'     => 'required|string|max:255',
            'contact_tel'          => 'required|string|max:50',
            'contact_email'        => 'required|email|max:255',
            'website'              => 'nullable|url|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'                 => 'business name',
            'business_category_id' => 'business category',
            'address'              => 'business address',
            'license_no'           => 'business license no.',
            'contact_person'       => 'contact person',
            'contact_position'     => 'position',
            'contact_tel'          => 'tel. no.',
            'contact_email'        => 'email',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Employer already exists.',
        ];
    }
}
