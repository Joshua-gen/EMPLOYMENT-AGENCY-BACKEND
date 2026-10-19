<?php

namespace Database\Seeders;

use App\Models\Management\Employer;
use App\Models\Management\JobOpening;
use Illuminate\Database\Seeder;
use RuntimeException;

class JobOpeningSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->jobs() as $job) {
            $employerNames = $job['employers'];
            $deadlineInDays = $job['deadline_in_days'];

            unset($job['employers'], $job['deadline_in_days']);

            // relative sa araw ng seeding, para hindi agad expired
            $job['deadline'] = now()->addDays($deadlineInDays)->toDateString();

            $employers = Employer::whereIn('name', $employerNames)->pluck('id', 'name');
            $missing = array_diff($employerNames, $employers->keys()->all());

            if ($missing) {
                throw new RuntimeException(
                    'Employer(s) not found: ' . implode(', ', $missing) . '. Run EmployerSeeder first.'
                );
            }

            $opening = JobOpening::updateOrCreate(
                ['title' => $job['title'], 'country' => $job['country']],
                $job
            );

            $opening->employers()->sync($employers->values()->all());
        }
    }

    private function describe(string $summary, array $duties, array $requirements, array $terms): string
    {
        $list = fn (array $items) => implode("\n", array_map(fn ($item) => "- {$item}", $items));

        return trim($summary)
            . "\n\nDuties:\n" . $list($duties)
            . "\n\nRequirements:\n" . $list($requirements)
            . "\n\nTerms:\n" . $list($terms);
    }

    private function jobs(): array
    {
        $taiwanRequirements = [
            'At least 20 years old',
            'Physically fit for factory work (medical exam at a DOH-accredited clinic)',
            'Completed PEOS and DMW worker e-registration',
        ];

        $taiwanTerms = [
            'Basic pay is at least the 2026 Taiwan minimum wage of NT$29,500 per month, plus overtime',
            'Labor insurance and national health insurance are mandatory',
            'Contract is usually 3 years and renewable',
            'Processed through a DMW-licensed agency; an OEC is required before departure',
        ];

        $japanRequirements = [
            'At least 18 years old',
            'Passed the skills test for the specified skilled worker field',
            'Passed the Japanese language test required for the status',
            'Passport valid for at least 6 months before the intended departure date',
        ];

        $japanTerms = [
            'Pay is meant to be equal to that of Japanese workers doing the same work',
            'Specified Skilled Worker (i) status allows a stay of up to 5 years',
            'Processed through a DMW-licensed agency; an OEC is required before departure',
        ];

        $koreaRequirements = [
            'Age 18 to 39',
            'Passed the EPS-TOPIK (results are valid for 2 years)',
            'Registered in the HRD Korea job seeker roster',
        ];

        $koreaTerms = [
            'Hired under the Employment Permit System (E-9 visa); job matching is done by HRD Korea',
            'Contract of up to 3 years, extendable to 4 years and 10 months',
            'Basic pay is at least the Korean minimum wage (about KRW 2,156,880 per month for 209 hours)',
            '40-hour work week plus overtime',
        ];

        return [
            // ---------------- TAIWAN ----------------
            [
                'title'            => 'Factory Worker',
                'country'          => 'Taiwan',
                'vacancies'        => '60',
                'salary'           => 'NT$ 29,500 - 35,000',
                'status'           => 'Published',
                'deadline_in_days' => 75,
                'employers'        => [
                    'Kook Electronics Manufacturing',
                    'Wiw Industrial Manufacturing',
                    'Taiwan Automotive Components',
                ],
                'description'      => $this->describe(
                    'General production line work in a manufacturing plant. Applicants are screened first, then matched to one of the participating employers.',
                    [
                        'Operate production line machines and equipment',
                        'Assemble, inspect, and package finished products',
                        'Keep the work area clean and follow plant safety procedures',
                        'Meet daily output and quality targets with the team',
                    ],
                    array_merge(['High school graduate or higher; vocational or college graduates are preferred'], $taiwanRequirements),
                    $taiwanTerms
                ),
            ],
            [
                'title'            => 'Electronics Assembly Operator',
                'country'          => 'Taiwan',
                'vacancies'        => '25',
                'salary'           => 'NT$ 29,500 - 35,000',
                'status'           => 'Published',
                'deadline_in_days' => 60,
                'employers'        => ['Kook Electronics Manufacturing'],
                'description'      => $this->describe(
                    'Assembly and inspection of electronic components and devices on a clean production line.',
                    [
                        'Assemble and solder small electronic parts following work instructions',
                        'Check finished units for defects and record results',
                        'Handle components carefully to prevent damage and static',
                        'Follow cleanroom and safety rules',
                    ],
                    array_merge(['Vocational or college graduate preferred', 'Good eyesight and steady hands'], $taiwanRequirements),
                    $taiwanTerms
                ),
            ],
            [
                'title'            => 'Warehouse Worker',
                'country'          => 'Taiwan',
                'vacancies'        => '15',
                'salary'           => 'NT$ 29,500 - 33,000',
                'status'           => 'Published',
                'deadline_in_days' => 45,
                'employers'        => ['Formosa Logistics Co.'],
                'description'      => $this->describe(
                    'Receiving, storing, and dispatching goods in a distribution warehouse.',
                    [
                        'Load, unload, and sort incoming and outgoing goods',
                        'Pick and pack orders accurately',
                        'Update stock records and report damaged items',
                        'Operate hand pallet trucks and follow warehouse safety rules',
                    ],
                    array_merge(['Able to lift and carry goods for long shifts'], $taiwanRequirements),
                    $taiwanTerms
                ),
            ],
            [
                'title'            => 'Food Processing Worker',
                'country'          => 'Taiwan',
                'vacancies'        => '20',
                'salary'           => 'NT$ 29,500 - 34,000',
                'status'           => 'Published',
                'deadline_in_days' => 50,
                'employers'        => ['Taiwan Fresh Foods Processing'],
                'description'      => $this->describe(
                    'Preparation, processing, and packaging of food products in a food factory.',
                    [
                        'Wash, cut, cook, or mix ingredients as instructed',
                        'Operate food processing and packaging machines',
                        'Follow hygiene and food safety rules at all times',
                        'Sort and label finished products for shipment',
                    ],
                    array_merge(['Comfortable working in cold or wet areas and in shifts'], $taiwanRequirements),
                    $taiwanTerms
                ),
            ],
            [
                'title'            => 'Garment Production Worker',
                'country'          => 'Taiwan',
                'vacancies'        => '18',
                'salary'           => 'NT$ 29,500 - 33,000',
                'status'           => 'Draft',
                'deadline_in_days' => 90,
                'employers'        => ['Formosa Garment Industries'],
                'description'      => $this->describe(
                    'Sewing and finishing of garments in a textile and apparel factory.',
                    [
                        'Operate industrial sewing machines to assemble garment pieces',
                        'Trim threads, press, and inspect finished garments',
                        'Fold, tag, and pack garments for shipment',
                        'Report machine problems and follow safety procedures',
                    ],
                    array_merge(['Sewing or garment experience is an advantage'], $taiwanRequirements),
                    $taiwanTerms
                ),
            ],
            [
                'title'            => 'Machine Operator',
                'country'          => 'Taiwan',
                'vacancies'        => '12',
                'salary'           => 'NT$ 29,500 - 37,000',
                'status'           => 'Closed',
                'deadline_in_days' => -10,
                'employers'        => [
                    'Wiw Industrial Manufacturing',
                    'Taiwan Automotive Components',
                ],
                'description'      => $this->describe(
                    'Operation of machining and metal forming equipment for industrial and automotive parts. This opening is closed to new applicants.',
                    [
                        'Set up and run machining or pressing equipment',
                        'Measure parts against drawings and quality standards',
                        'Perform basic machine maintenance and cleaning',
                        'Follow lockout and safety procedures',
                    ],
                    array_merge(['Machine operation experience or TESDA certificate preferred'], $taiwanRequirements),
                    $taiwanTerms
                ),
            ],

            // ---------------- JAPAN ----------------
            [
                'title'            => 'Food Manufacturing Worker (Specified Skilled Worker)',
                'country'          => 'Japan',
                'vacancies'        => '30',
                'salary'           => 'Same as Japanese workers (prefecture minimum wage or higher)',
                'status'           => 'Published',
                'deadline_in_days' => 80,
                'employers'        => ['Osaka Food Processing', 'Hokkaido Food Industries'],
                'description'      => $this->describe(
                    'Food and beverage manufacturing under the Specified Skilled Worker program.',
                    [
                        'Prepare, process, and cook food products in a production line',
                        'Operate and clean food processing machines',
                        'Check product quality and follow hygiene standards',
                        'Pack and label finished products',
                    ],
                    $japanRequirements,
                    $japanTerms
                ),
            ],
            [
                'title'            => 'Industrial Machinery Parts Operator (Specified Skilled Worker)',
                'country'          => 'Japan',
                'vacancies'        => '20',
                'salary'           => 'Same as Japanese workers (prefecture minimum wage or higher)',
                'status'           => 'Published',
                'deadline_in_days' => 70,
                'employers'        => ['Nippon Precision Ltd.', 'Sakura Automotive Parts'],
                'description'      => $this->describe(
                    'Manufacturing of machine parts and tooling under the Specified Skilled Worker program.',
                    [
                        'Operate machining, pressing, or assembly equipment for machine parts',
                        'Read basic drawings and check dimensions with measuring tools',
                        'Maintain tools and keep the production area organized',
                        'Follow Japanese workplace safety practices (5S)',
                    ],
                    $japanRequirements,
                    $japanTerms
                ),
            ],
            [
                'title'            => 'Electronics Assembly Technician (Specified Skilled Worker)',
                'country'          => 'Japan',
                'vacancies'        => '15',
                'salary'           => 'Same as Japanese workers (prefecture minimum wage or higher)',
                'status'           => 'Published',
                'deadline_in_days' => 65,
                'employers'        => ['Tokyo Electronics Assembly'],
                'description'      => $this->describe(
                    'Assembly and testing of electrical and electronic products under the Specified Skilled Worker program.',
                    [
                        'Assemble circuit boards and electronic units',
                        'Test finished products and record the results',
                        'Identify and report defects',
                        'Follow cleanroom and electrostatic safety rules',
                    ],
                    $japanRequirements,
                    $japanTerms
                ),
            ],

            // ---------------- SOUTH KOREA ----------------
            [
                'title'            => 'Manufacturing Worker (EPS)',
                'country'          => 'South Korea',
                'vacancies'        => '80',
                'salary'           => 'KRW 2,156,880+ per month',
                'status'           => 'Published',
                'deadline_in_days' => 85,
                'employers'        => [
                    'Hanbit Automotive Manufacturing',
                    'Seoul Electronics Industries',
                    'Ulsan Industrial Manufacturing',
                    'Daegu Textile Manufacturing',
                    'Incheon Food Processing',
                ],
                'description'      => $this->describe(
                    'Production work in Korean manufacturing companies under the Employment Permit System.',
                    [
                        'Operate machines and equipment on the production line',
                        'Assemble, inspect, and package products',
                        'Follow factory safety rules and company procedures',
                        'Work with Korean co-workers and supervisors, using basic Korean',
                    ],
                    $koreaRequirements,
                    $koreaTerms
                ),
            ],
            [
                'title'            => 'Shipyard Production Worker (EPS)',
                'country'          => 'South Korea',
                'vacancies'        => '10',
                'salary'           => 'KRW 2,156,880+ per month',
                'status'           => 'Draft',
                'deadline_in_days' => 100,
                'employers'        => ['Busan Shipyard Industries'],
                'description'      => $this->describe(
                    'Production support in shipbuilding under the Employment Permit System.',
                    [
                        'Assist in assembling and finishing ship structures and parts',
                        'Prepare surfaces, grind, and clean work areas',
                        'Handle tools and materials safely, including work at heights',
                        'Wear protective equipment and follow yard safety rules',
                    ],
                    $koreaRequirements,
                    $koreaTerms
                ),
            ],
        ];
    }
}
