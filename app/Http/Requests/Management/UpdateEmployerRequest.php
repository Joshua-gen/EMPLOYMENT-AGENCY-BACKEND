<?php

namespace App\Http\Requests\Management;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployerRequest extends FormRequest
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

        if (! empty($data['website']) && ! preg_match('#^https?://#i', $data['website'])) {
            $data['website'] = 'https://' . $data['website'];
        }

        $this->merge($data);
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name'                 => ['sometimes', 'required', 'string', 'max:255', Rule::unique('employers', 'name')->ignore($id)],
            'business_category_id' => 'sometimes|required|integer|exists:business_categories,id',
            'country'              => 'sometimes|required|string|max:255',
            'address'              => 'sometimes|required|string|max:500',
            'license_no'           => ['sometimes', 'nullable', 'string', 'max:100', Rule::unique('employers', 'license_no')->ignore($id)],
            'contact_person'       => 'sometimes|required|string|max:255',
            'contact_position'     => 'sometimes|required|string|max:255',
            'contact_tel'          => 'sometimes|required|string|max:50',
            'contact_email'        => 'sometimes|required|email|max:255',
            'website'              => 'sometimes|nullable|url|max:255',
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
