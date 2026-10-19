<?php

namespace Database\Seeders;

use App\Models\Management\BusinessCategory;
use App\Models\Management\Employer;
use Illuminate\Database\Seeder;
use RuntimeException;

class EmployerSeeder extends Seeder
{
    public function run(): void
    {
        $employers = [
            // TAIWAN
            [
                'name' => 'Formosa Logistics Co.',
                'category' => 'Warehouse & Logistics',
                'country' => 'Taiwan',
                'address' => 'Kaohsiung City, Taiwan',
                'license_no' => 'TW-KH-2015-04821',
                'contact_person' => 'Chen Wei-Lun',
                'contact_position' => 'HR Manager',
                'contact_tel' => '+886 7 123 4567',
                'contact_email' => 'hr@formosalogistics.example',
                'website' => null,
            ],
            [
                'name' => 'Kook Electronics Manufacturing',
                'category' => 'Electronics & Electrical Manufacturing',
                'country' => 'Taiwan',
                'address' => 'Taoyuan City, Taiwan',
                'license_no' => 'TW-TY-2012-01934',
                'contact_person' => 'Lin Mei-Hua',
                'contact_position' => 'Recruitment Supervisor',
                'contact_tel' => '+886 3 234 5678',
                'contact_email' => 'recruitment@kookcorp.example',
                'website' => null,
            ],
            [
                'name' => 'Wiw Industrial Manufacturing',
                'category' => 'Manufacturing & Factory Work',
                'country' => 'Taiwan',
                'address' => 'Taichung City, Taiwan',
                'license_no' => 'TW-TC-2018-07765',
                'contact_person' => 'Wang Jian-Hong',
                'contact_position' => 'General Affairs Manager',
                'contact_tel' => '+886 4 345 6789',
                'contact_email' => 'ga@wiwcorp.example',
                'website' => null,
            ],
            [
                'name' => 'Taiwan Fresh Foods Processing',
                'category' => 'Food Processing & Packaging',
                'country' => 'Taiwan',
                'address' => 'Tainan City, Taiwan',
                'license_no' => 'TW-TN-2019-01452',
                'contact_person' => 'Huang Chih-Wei',
                'contact_position' => 'Production Manager',
                'contact_tel' => '+886 6 245 7812',
                'contact_email' => 'hr@taiwanfresh.example',
                'website' => null,
            ],
            [
                'name' => 'Formosa Garment Industries',
                'category' => 'Garments & Textile Manufacturing',
                'country' => 'Taiwan',
                'address' => 'New Taipei City, Taiwan',
                'license_no' => 'TW-NT-2020-02561',
                'contact_person' => 'Liu Ya-Ting',
                'contact_position' => 'HR Supervisor',
                'contact_tel' => '+886 2 2678 1234',
                'contact_email' => 'jobs@formosagarment.example',
                'website' => null,
            ],
            [
                'name' => 'Taiwan Automotive Components',
                'category' => 'Automotive & Parts Manufacturing',
                'country' => 'Taiwan',
                'address' => 'Hsinchu City, Taiwan',
                'license_no' => 'TW-HC-2021-03672',
                'contact_person' => 'Wu Chia-Ming',
                'contact_position' => 'Factory Manager',
                'contact_tel' => '+886 3 567 8901',
                'contact_email' => 'hr@taiwanauto.example',
                'website' => null,
            ],

            // JAPAN
            [
                'name' => 'Nippon Precision Ltd.',
                'category' => 'Manufacturing & Factory Work',
                'country' => 'Japan',
                'address' => 'Nagoya, Aichi, Japan',
                'license_no' => 'JP-AI-2008-55218',
                'contact_person' => 'Tanaka Hiroshi',
                'contact_position' => 'Personnel Manager',
                'contact_tel' => '+81 52 567 8901',
                'contact_email' => 'personnel@nipponprecision.example',
                'website' => null,
            ],
            [
                'name' => 'Sakura Automotive Parts',
                'category' => 'Automotive & Parts Manufacturing',
                'country' => 'Japan',
                'address' => 'Toyota City, Aichi, Japan',
                'license_no' => 'JP-AI-2016-10432',
                'contact_person' => 'Sato Kenji',
                'contact_position' => 'Recruitment Manager',
                'contact_tel' => '+81 565 234 567',
                'contact_email' => 'recruit@sakuraauto.example',
                'website' => null,
            ],
            [
                'name' => 'Osaka Food Processing',
                'category' => 'Food Processing & Packaging',
                'country' => 'Japan',
                'address' => 'Osaka, Japan',
                'license_no' => 'JP-OS-2019-20314',
                'contact_person' => 'Yamada Akira',
                'contact_position' => 'HR Officer',
                'contact_tel' => '+81 6 2345 6789',
                'contact_email' => 'hr@osakafoods.example',
                'website' => null,
            ],
            [
                'name' => 'Tokyo Electronics Assembly',
                'category' => 'Electronics & Electrical Manufacturing',
                'country' => 'Japan',
                'address' => 'Tokyo, Japan',
                'license_no' => 'JP-TK-2020-31425',
                'contact_person' => 'Suzuki Haruto',
                'contact_position' => 'Production Supervisor',
                'contact_tel' => '+81 3 4567 8901',
                'contact_email' => 'jobs@tokyoelectronics.example',
                'website' => null,
            ],
            [
                'name' => 'Hokkaido Food Industries',
                'category' => 'Food Processing & Packaging',
                'country' => 'Japan',
                'address' => 'Sapporo, Hokkaido, Japan',
                'license_no' => 'JP-HD-2021-41526',
                'contact_person' => 'Ito Yuki',
                'contact_position' => 'Recruitment Officer',
                'contact_tel' => '+81 11 234 5678',
                'contact_email' => 'recruit@hokkaidofoods.example',
                'website' => null,
            ],
            [
                'name' => 'Kyoto Textile Manufacturing',
                'category' => 'Garments & Textile Manufacturing',
                'country' => 'Japan',
                'address' => 'Kyoto, Japan',
                'license_no' => 'JP-KY-2018-51637',
                'contact_person' => 'Kobayashi Ren',
                'contact_position' => 'HR Manager',
                'contact_tel' => '+81 75 234 5678',
                'contact_email' => 'hr@kyototextile.example',
                'website' => null,
            ],

            // SOUTH KOREA
            [
                'name' => 'Hanbit Automotive Manufacturing',
                'category' => 'Automotive & Parts Manufacturing',
                'country' => 'South Korea',
                'address' => 'Incheon, South Korea',
                'license_no' => 'KR-IC-2010-33120',
                'contact_person' => 'Park Ji-Hoon',
                'contact_position' => 'HR Director',
                'contact_tel' => '+82 32 456 7890',
                'contact_email' => 'hr@hanbit.example',
                'website' => null,
            ],
            [
                'name' => 'Seoul Electronics Industries',
                'category' => 'Electronics & Electrical Manufacturing',
                'country' => 'South Korea',
                'address' => 'Seoul, South Korea',
                'license_no' => 'KR-SE-2017-42731',
                'contact_person' => 'Kim Min-Jun',
                'contact_position' => 'Recruitment Manager',
                'contact_tel' => '+82 2 3456 7890',
                'contact_email' => 'jobs@seoulelectronics.example',
                'website' => null,
            ],
            [
                'name' => 'Busan Shipyard Industries',
                'category' => 'Shipbuilding & Shipyard Services',
                'country' => 'South Korea',
                'address' => 'Busan, South Korea',
                'license_no' => 'KR-BS-2015-53842',
                'contact_person' => 'Choi Seo-Jun',
                'contact_position' => 'Manpower Coordinator',
                'contact_tel' => '+82 51 234 5678',
                'contact_email' => 'hr@busanshipyard.example',
                'website' => null,
            ],
            [
                'name' => 'Daegu Textile Manufacturing',
                'category' => 'Garments & Textile Manufacturing',
                'country' => 'South Korea',
                'address' => 'Daegu, South Korea',
                'license_no' => 'KR-DG-2019-64953',
                'contact_person' => 'Jung Ha-Neul',
                'contact_position' => 'HR Supervisor',
                'contact_tel' => '+82 53 234 5678',
                'contact_email' => 'recruit@daegutextile.example',
                'website' => null,
            ],
            [
                'name' => 'Incheon Food Processing',
                'category' => 'Food Processing & Packaging',
                'country' => 'South Korea',
                'address' => 'Incheon, South Korea',
                'license_no' => 'KR-IC-2021-75064',
                'contact_person' => 'Lee Ji-Woo',
                'contact_position' => 'Production Manager',
                'contact_tel' => '+82 32 567 8901',
                'contact_email' => 'jobs@incheonfoods.example',
                'website' => null,
            ],
            [
                'name' => 'Ulsan Industrial Manufacturing',
                'category' => 'Manufacturing & Factory Work',
                'country' => 'South Korea',
                'address' => 'Ulsan, South Korea',
                'license_no' => 'KR-US-2022-86175',
                'contact_person' => 'Kang Do-Yun',
                'contact_position' => 'Factory Operations Manager',
                'contact_tel' => '+82 52 345 6789',
                'contact_email' => 'hr@ulsanindustrial.example',
                'website' => null,
            ],
        ];

        foreach ($employers as $employerData) {
            $categoryName = $employerData['category'];

            unset($employerData['category']);

            $category = BusinessCategory::where('name', $categoryName)
                ->first();

            if (!$category) {
                throw new RuntimeException(
                    "Business category '{$categoryName}' not found. "
                    . 'Run BusinessCategorySeeder first.'
                );
            }

            $employerData['business_category_id'] = $category->id;
            $employerData['business_category_name'] = $category->name;

            Employer::updateOrCreate(
                ['name' => $employerData['name']],
                $employerData
            );
        }
    }
}
