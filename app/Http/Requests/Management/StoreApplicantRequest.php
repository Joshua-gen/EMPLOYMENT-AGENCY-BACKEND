<?php

namespace App\Http\Requests\Management;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $jobId = $this->input('job_opening_id');
        $email = $this->input('email');
        $year = (int) now()->format('Y');
        $thisMonth = now()->format('Y-m');

        return [
            // walang laman = wala pang job; Published lang ang pwede
            'job_opening_id' => [
                'nullable',
                'integer',
                Rule::exists('job_openings', 'id')->where('status', 'Published'),
            ],
            'applied_at' => 'nullable|date|before_or_equal:today',

            // Personal
            'name'         => 'required|string|max:255',
            'phone'        => 'required|string|max:50',
            'birthday'     => 'required|date|before:today',
            'gender'       => 'required|in:male,female',
            'email'        => array_filter([
                'nullable',
                'email',
                'max:255',
                $jobId && $email ? Rule::unique('applicants', 'email')->where('job_opening_id', $jobId) : null,
            ]),
            'civil_status' => 'nullable|in:single,married,widowed,separated',
            'address'      => 'nullable|string|max:500',

            // Education
            'educational_attainment'   => 'nullable|in:elementary,high-school,senior-high,vocational,college,post-graduate',
            'school'                   => 'nullable|string|max:255',
            'course'                   => 'nullable|string|max:255',
            'education_start_year'     => ['nullable', 'integer', 'min:1950', "max:{$year}"],
            'education_graduated_year' => [
                'nullable',
                'integer',
                'min:1950',
                "max:{$year}",
                function ($attribute, $value, $fail) {
                    $start = $this->input('education_start_year');

                    if ($start !== null && $start !== '' && (int) $value < (int) $start) {
                        $fail('The graduated year cannot be earlier than the start year.');
                    }
                },
            ],

            // Work experience (YYYY-MM)
            'previous_employer'         => 'nullable|string|max:255',
            'previous_employer_address' => 'nullable|string|max:500',
            'previous_position'         => 'nullable|string|max:255',
            'work_start'                => [
                'nullable',
                'required_with:previous_employer',
                'date_format:Y-m',
                function ($attribute, $value, $fail) use ($thisMonth) {
                    if ($value > $thisMonth) {
                        $fail('The start date cannot be in the future.');
                    }
                },
            ],
            'work_end' => [
                'nullable',
                'date_format:Y-m',
                function ($attribute, $value, $fail) use ($thisMonth) {
                    if ($value > $thisMonth) {
                        $fail('The end date cannot be in the future.');
                    }

                    $start = $this->input('work_start');

                    if ($start && $value < $start) {
                        $fail('The end date cannot be earlier than the start date.');
                    }
                },
            ],

            'skills'         => 'nullable|string|max:2000',
            'certifications' => 'nullable|string|max:2000',
            'languages'      => 'nullable|string|max:255',

            // Documents: lahat optional sa admin entry
            'resume'             => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'valid_id'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'passport'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'other_requirements' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ];
    }

    public function attributes(): array
    {
        return [
            'job_opening_id'            => 'job opening',
            'civil_status'              => 'civil status',
            'educational_attainment'    => 'educational attainment',
            'education_start_year'      => 'start year',
            'education_graduated_year'  => 'graduated year',
            'previous_employer'         => 'previous employer',
            'previous_employer_address' => 'company address',
            'previous_position'         => 'previous position',
            'work_start'                => 'start date',
            'work_end'                  => 'end date',
            'applied_at'                => 'date applied',
            'valid_id'                  => 'valid ID',
            'other_requirements'        => 'other requirements',
        ];
    }

    public function messages(): array
    {
        return [
            'job_opening_id.exists' => 'Only published job openings can be selected.',
            'email.unique'          => 'This email has already applied to the selected job opening.',
            'work_start.required_with' => 'Start date is required when you provide a previous employer.',
        ];
    }
}
