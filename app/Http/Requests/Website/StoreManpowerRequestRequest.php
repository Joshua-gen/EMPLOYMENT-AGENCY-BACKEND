<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreManpowerRequestRequest extends FormRequest
{
    private const TRIMMED = [
        'business_name', 'business_address', 'country', 'license_no',
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
            // Honeypot: nakatago sa form, hindi dapat sinasagutan ng tao
            'contact_fax' => 'prohibited',

            'business_name'        => 'required|string|max:255',
            'business_category_id' => 'required|integer|exists:business_categories,id',
            'country'              => ['required', 'string', 'max:255', Rule::exists('countries', 'name')],
            'business_address'     => 'required|string|max:500',
            'license_no'           => 'nullable|string|max:100',

            'contact_person'   => 'required|string|max:255',
            'contact_position' => 'required|string|max:255',
            'contact_tel'      => 'required|string|max:50',
            'contact_email'    => 'required|email|max:255',
            'website'          => 'nullable|url|max:255',

            'positions'                   => 'required|array|min:1|max:10',
            'positions.*.position'        => 'required|string|max:255|distinct:ignore_case',
            'positions.*.number_required' => 'required|integer|min:1|max:10000',
            'positions.*.description'     => 'required|string|max:2000',
            'g_recaptcha_response' => [
                'required',
                'string',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'business_name'               => 'business name',
            'business_category_id'        => 'nature of business',
            'business_address'            => 'business address',
            'license_no'                  => 'business license no.',
            'contact_person'              => 'contact person',
            'contact_position'            => 'position',
            'contact_tel'                 => 'tel. no.',
            'contact_email'               => 'email',
            'positions'                   => 'job positions',
            'positions.*.position'        => 'position',
            'positions.*.number_required' => 'number required',
            'positions.*.description'     => 'job description',
            'g_recaptcha_response'        => 'reCAPTCHA verification',
        ];
    }

    public function messages(): array
    {
        return [
            'country.exists'                  => 'Please select a valid country.',
            'positions.max'                   => 'You can add up to 10 job positions.',
            'positions.*.position.distinct'   => 'Each position can only be listed once.',
        ];
    }
}
