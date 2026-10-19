<?php

namespace Database\Seeders;

use App\Models\Management\Applicant;
use App\Models\Management\Employer;
use App\Models\Management\JobOpening;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use RuntimeException;

class ApplicantSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->applicants() as $row) {
            $jobKey = Arr::pull($row, 'job');
            $employerName = Arr::pull($row, 'employer');

            $job = null;
            $employer = null;

            if ($jobKey) {
                [$title, $country] = $jobKey;

                $job = JobOpening::where('title', $title)->where('country', $country)->first();

                if (! $job) {
                    throw new RuntimeException("Job opening '{$title}' ({$country}) not found. Run JobOpeningSeeder first.");
                }
            }

            if ($employerName) {
                $employer = Employer::where('name', $employerName)->first();

                if (! $employer || ! $job || ! $job->employers()->whereKey($employer->id)->exists()) {
                    throw new RuntimeException("Employer '{$employerName}' is not part of the job opening of {$row['name']}.");
                }
            }

            $applicant = Applicant::updateOrCreate(
                ['email' => $row['email']],
                $row + ['job_opening_id' => $job?->id, 'employer_id' => $employer?->id]
            );

            $applicant->update([
                'reference' => sprintf('APP-%s-%03d', $applicant->applied_at->format('Y'), $applicant->id),
            ]);
        }
    }

    private function row(string $name, string $status, ?array $job, ?string $employer, array $extra = []): array
    {
        return array_merge([
            'name'                      => $name,
            'email'                     => Str::slug($name, '.') . '@example.com',
            'phone'                     => '0917' . str_pad((string) (crc32($name) % 10000000), 7, '0', STR_PAD_LEFT),
            'birthday'                  => '1996-05-14',
            'gender'                    => 'male',
            'civil_status'              => 'single',
            'address'                   => 'Quezon City, Metro Manila',
            'educational_attainment'    => 'high-school',
            'school'                    => 'Quezon City High School',
            'course'                    => null,
            'education_start_year'      => 2008,
            'education_graduated_year'  => 2012,
            'previous_employer'         => null,
            'previous_employer_address' => null,
            'previous_position'         => null,
            'work_start'                => null,
            'work_end'                  => null,
            'skills'                    => 'Machine operation, packing, basic troubleshooting',
            'certifications'            => null,
            'languages'                 => 'Filipino, English',
            'remarks'                   => null,
            'applied_at'                => '2026-10-01',
            'status'                    => $status,
            'source'                    => $job ? 'job' : 'direct',
            'job'                       => $job,
            'employer'                  => $employer,
        ], $extra);
    }

    private function applicants(): array
    {
        $factory = ['Factory Worker', 'Taiwan'];

        return [
            $this->row('Mark Anthony Santos', 'Submitted', $factory, null),

            $this->row('Maria Angela Reyes', 'Under Review', ['Electronics Assembly Operator', 'Taiwan'], null, [
                'gender'                    => 'female',
                'remarks'                   => 'Initial documents received.',
                'applied_at'                => '2026-10-02',
                'previous_employer'         => 'Greenfield Packaging Corp.',
                'previous_employer_address' => 'Carmona, Cavite, Philippines',
                'previous_position'         => 'Packer',
                'work_start'                => '2021-03-01',
                'work_end'                  => '2023-08-01',
            ]),

            $this->row('John Michael Cruz', 'Shortlisted',
                ['Industrial Machinery Parts Operator (Specified Skilled Worker)', 'Japan'], null, [
                    'remarks'    => 'Qualified for initial interview.',
                    'applied_at' => '2026-10-02',
                ]),

            $this->row('Angelica Mae Garcia', 'Interview',
                ['Food Manufacturing Worker (Specified Skilled Worker)', 'Japan'], 'Osaka Food Processing', [
                    'gender'     => 'female',
                    'remarks'    => 'Interview scheduled.',
                    'applied_at' => '2026-10-03',
                ]),

            $this->row('Daniel Joseph Mendoza', 'Selected', $factory, 'Kook Electronics Manufacturing', [
                'remarks'    => 'Selected by the employer.',
                'applied_at' => '2026-10-03',
            ]),

            $this->row('Patricia Anne Flores', 'Processing',
                ['Manufacturing Worker (EPS)', 'South Korea'], 'Seoul Electronics Industries', [
                    'gender'     => 'female',
                    'remarks'    => 'Processing pre-departure requirements.',
                    'applied_at' => '2026-10-04',
                ]),

            $this->row('Kevin Rafael Torres', 'Deployed', ['Warehouse Worker', 'Taiwan'], 'Formosa Logistics Co.', [
                'remarks'    => 'Deployment completed.',
                'applied_at' => '2026-10-04',
            ]),

            // Direct apply, wala pang job
            $this->row('Christine Joy Ramos', 'Rejected', null, null, [
                'gender'     => 'female',
                'remarks'    => 'Does not meet the requirements.',
                'applied_at' => '2026-10-05',
            ]),

            $this->row('Ramon Luis Bautista', 'Submitted', null, null, ['applied_at' => '2026-10-05']),

            $this->row('Sofia Nicole Castillo', 'Under Review', null, null, [
                'gender'     => 'female',
                'remarks'    => 'Passport copy needs verification.',
                'applied_at' => '2026-10-06',
            ]),

            // kasalukuyang nagtatrabaho (walang work_end)
            $this->row('Adrian Paul Villanueva', 'Interview', $factory, 'Wiw Industrial Manufacturing', [
                'remarks'                   => 'For final interview.',
                'applied_at'                => '2026-10-06',
                'previous_employer'         => 'Laguna Metal Works',
                'previous_employer_address' => 'Calamba, Laguna, Philippines',
                'previous_position'         => 'Machine Operator',
                'work_start'                => '2022-06-01',
            ]),

            $this->row('Beatrice Anne Lim', 'Shortlisted', null, null, [
                'gender'     => 'female',
                'applied_at' => '2026-10-06',
            ]),

            // Direct apply na na-assign na ni admin sa job
            $this->row('Carlo Miguel Dizon', 'Under Review',
                ['Electronics Assembly Technician (Specified Skilled Worker)', 'Japan'], null, [
                    'source'     => 'direct',
                    'remarks'    => 'Assigned by admin.',
                    'applied_at' => '2026-10-07',
                ]),
        ];
    }
}
