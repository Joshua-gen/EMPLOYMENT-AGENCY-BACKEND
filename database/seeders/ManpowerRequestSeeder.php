<?php

namespace Database\Seeders;

use App\Models\Management\BusinessCategory;
use App\Models\Management\Employer;
use App\Models\Management\JobOpening;
use App\Models\Management\ManpowerRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use RuntimeException;

class ManpowerRequestSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->requests() as $row) {
            $positions = Arr::pull($row, 'positions');
            $categoryName = Arr::pull($row, 'category');
            $employerName = Arr::pull($row, 'employer');
            $link = Arr::pull($row, 'link');

            $category = BusinessCategory::where('name', $categoryName)->first();

            if (! $category) {
                throw new RuntimeException("Business category '{$categoryName}' not found. Run BusinessCategorySeeder first.");
            }

            $employer = null;

            if ($employerName) {
                $employer = Employer::where('name', $employerName)->first();

                if (! $employer) {
                    throw new RuntimeException("Employer '{$employerName}' not found. Run EmployerSeeder first.");
                }
            }

            $request = ManpowerRequest::updateOrCreate(
                ['business_name' => $row['business_name'], 'requested_at' => $row['requested_at']],
                $row + ['business_category_id' => $category->id, 'employer_id' => $employer?->id]
            );

            if (! $request->reference) {
                $request->update([
                    'reference' => sprintf('MR-%s-%03d', $request->requested_at->format('Y'), $request->id),
                ]);
            }

            // Para hindi madoble kapag inulit ang seeder
            if (! $request->wasRecentlyCreated) {
                continue;
            }

            $request->positions()->createMany($positions);

            if ($link && $employer) {
                [$title, $country] = $link;

                $job = JobOpening::where('title', $title)->where('country', $country)->first();

                if (! $job) {
                    throw new RuntimeException("Job opening '{$title}' ({$country}) not found. Run JobOpeningSeeder first.");
                }

                $position = $request->positions()->first();

                $existing = $job->employers()->whereKey($employer->id)->first();
                $current = (int) ($existing?->demand?->vacancies ?? 0);

                $job->employers()->syncWithoutDetaching([$employer->id]);
                $position->update(['job_opening_id' => $job->id]);
            }
        }
    }

    private function position(string $position, int $required, string $description): array
    {
        return ['position' => $position, 'number_required' => $required, 'description' => $description];
    }

    private function row(
        string $name,
        string $category,
        string $country,
        string $address,
        string $status,
        string $date,
        array $positions,
        array $extra = []
    ): array {
        $slug = Str::slug($name);

        return array_merge([
            'business_name'    => $name,
            'category'         => $category,
            'country'          => $country,
            'business_address' => $address,
            'license_no'       => null,
            'contact_person'   => 'HR Department',
            'contact_position' => 'HR Manager',
            'contact_tel'      => '+886 2 ' . str_pad((string) (crc32($name) % 100000000), 8, '0', STR_PAD_LEFT),
            'contact_email'    => "hr@{$slug}.example",
            'website'          => null,
            'status'           => $status,
            'requested_at'     => $date,
            'remarks'          => null,
            'reviewed_at'      => $status === 'Pending' ? null : now(),
            'employer'         => null,
            'link'             => null,
            'positions'        => $positions,
        ], $extra);
    }

    private function requests(): array
    {
        return [
            $this->row('Pacific Manufacturing Co.', 'Manufacturing & Factory Work', 'Taiwan', 'Taoyuan City, Taiwan', 'Pending', '2026-10-01', [
                $this->position('Factory Worker', 25, 'Operate production line machines and assemble products.'),
                $this->position('Machine Operator', 10, 'Set up and run machining equipment.'),
            ], ['contact_person' => 'Robert Dela Cruz']),

            $this->row('Evergreen Food Industries', 'Food Processing & Packaging', 'Taiwan', 'Tainan City, Taiwan', 'Pending', '2026-10-08', [
                $this->position('Food Production Worker', 18, 'Prepare and package food products following hygiene standards.'),
            ], ['contact_person' => 'Adrian Villanueva']),

            $this->row('Daehan Components Co.', 'Electronics & Electrical Manufacturing', 'South Korea', 'Incheon, South Korea', 'Pending', '2026-10-06', [
                $this->position('Production Operator', 20, 'Operate assembly equipment and follow safety standards.'),
            ], ['contact_person' => 'Kevin Bautista']),

            // Approved at naka-link na sa job (may bilang na sa vacancies ng employer)
            $this->row('Taiwan Fresh Foods Processing', 'Food Processing & Packaging', 'Taiwan', 'Tainan City, Taiwan', 'Approved', '2026-10-02', [
                $this->position('Food Processing Worker', 20, 'Process and pack food products in a factory setting.'),
            ], [
                'contact_person' => 'Huang Chih-Wei',
                'remarks'        => 'Linked to the existing Food Processing Worker opening.',
                'employer'       => 'Taiwan Fresh Foods Processing',
                'link'           => ['Food Processing Worker', 'Taiwan'],
            ]),

            // Approved pero wala pang job
            $this->row('Osaka Food Processing', 'Food Processing & Packaging', 'Japan', 'Osaka, Japan', 'Approved', '2026-10-07', [
                $this->position('Food Manufacturing Worker', 12, 'Food manufacturing under the Specified Skilled Worker program.'),
            ], [
                'contact_person' => 'Yamada Akira',
                'remarks'        => 'Approved. Waiting for a matching job opening.',
                'employer'       => 'Osaka Food Processing',
            ]),

            $this->row('Northstar Industrial Services', 'Manufacturing & Factory Work', 'South Korea', 'Ulsan, South Korea', 'Rejected', '2026-10-04', [
                $this->position('Construction Worker', 15, 'Construction support work.'),
            ], [
                'contact_person' => 'Daniel Garcia',
                'remarks'        => 'Incomplete employer and job requirement information.',
            ]),
        ];
    }
}
