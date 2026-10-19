<?php

namespace Database\Seeders;

use App\Models\Management\BusinessCategory;
use Illuminate\Database\Seeder;

class BusinessCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Agriculture & Farming',
            'Aquaculture & Fishing',
            'Automotive & Auto Repair',
            'Construction & Skilled Trades',
            'Domestic & Household Services',
            'Cleaning & Janitorial Services',
            'Elderly & Caregiving Services',
            'Healthcare & Medical Services',
            'Manufacturing & Factory Work',
            'Food Processing & Packaging',
            'Food Service & Restaurants',
            'Hospitality & Hotels',
            'Retail & Sales',
            'Warehouse & Logistics',
            'Transportation & Driving',
            'Security Services',
            'Building Maintenance & Facilities',
            'Electrical & Mechanical Services',
            'Welding & Metal Fabrication',
            'Shipbuilding & Shipyard Services',
            'Maritime & Seafaring',
            'Oil, Gas & Energy',
            'Mining & Quarrying',
            'Garments & Textile Manufacturing',
            'Electronics & Electrical Manufacturing',
            'Automotive & Parts Manufacturing',
            'Furniture & Woodworking',
            'General Labor & Industrial Services',
            'Beauty & Personal Care',
            'Education & Teaching',
            'Information Technology',
            'Office Administration & Clerical Services',
            'Customer Service & Call Center',
            'Engineering & Technical Services',
            'Professional & Management Services',
            'Tourism & Travel',
            'Waste Management & Recycling',
            'Other Industries',
        ];

        foreach ($categories as $name) {
            BusinessCategory::updateOrCreate(
                ['name' => $name]
            );
        }
    }
}
