<?php

namespace App\Http\Controllers\Api\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DeploymentMapController extends Controller
{
    /**
     * Mock coordinates: [longitude, latitude].
     */
    private const COUNTRY_COORDINATES = [
        'Taiwan' => [120.9605, 23.6978],
        'Japan' => [138.2529, 36.2048],
        'South Korea' => [127.7669, 35.9078],
        'Saudi Arabia' => [45.0792, 23.8859],
        'United Arab Emirates' => [53.8478, 23.4241],
        'Qatar' => [51.1839, 25.3548],
        'Singapore' => [103.8198, 1.3521],
        'Hong Kong' => [114.1694, 22.3193],
        'Canada' => [-106.3468, 56.1304],
        'United States' => [-98.5795, 39.8283],
        'United Kingdom' => [-3.4360, 55.3781],
        'Germany' => [10.4515, 51.1657],
        'Italy' => [12.5674, 41.8719],
        'Australia' => [133.7751, -25.2744],
        'New Zealand' => [174.8860, -40.9006],
    ];

    public function index(): JsonResponse
    {
        $mockDeployments = [
            'Taiwan' => [
                'applicants' => 125,
                'positions' => [
                    ['title' => 'Factory Worker', 'applicants' => 75],
                    ['title' => 'Production Operator', 'applicants' => 30],
                    ['title' => 'Machine Technician', 'applicants' => 20],
                ],
            ],
            'Japan' => [
                'applicants' => 98,
                'positions' => [
                    ['title' => 'Caregiver', 'applicants' => 35],
                    ['title' => 'Factory Worker', 'applicants' => 40],
                    ['title' => 'Skilled Worker', 'applicants' => 23],
                ],
            ],
            'South Korea' => [
                'applicants' => 86,
                'positions' => [
                    ['title' => 'Manufacturing Worker', 'applicants' => 56],
                    ['title' => 'Production Worker', 'applicants' => 30],
                ],
            ],
            'Saudi Arabia' => [
                'applicants' => 72,
                'positions' => [
                    ['title' => 'Construction Worker', 'applicants' => 32],
                    ['title' => 'Hotel Staff', 'applicants' => 20],
                    ['title' => 'Domestic Worker', 'applicants' => 20],
                ],
            ],
            'United Arab Emirates' => [
                'applicants' => 64,
                'positions' => [
                    ['title' => 'Hospitality Staff', 'applicants' => 24],
                    ['title' => 'Construction Worker', 'applicants' => 25],
                    ['title' => 'Service Crew', 'applicants' => 15],
                ],
            ],
            'Qatar' => [
                'applicants' => 43,
                'positions' => [
                    ['title' => 'Construction Worker', 'applicants' => 25],
                    ['title' => 'Hotel Staff', 'applicants' => 18],
                ],
            ],
            'Singapore' => [
                'applicants' => 38,
                'positions' => [
                    ['title' => 'Service Crew', 'applicants' => 18],
                    ['title' => 'Technician', 'applicants' => 20],
                ],
            ],
            'Hong Kong' => [
                'applicants' => 52,
                'positions' => [
                    ['title' => 'Domestic Worker', 'applicants' => 52],
                ],
            ],
            'Canada' => [
                'applicants' => 28,
                'positions' => [
                    ['title' => 'Caregiver', 'applicants' => 16],
                    ['title' => 'Farm Worker', 'applicants' => 12],
                ],
            ],
            'United States' => [
                'applicants' => 21,
                'positions' => [
                    ['title' => 'Hospitality Staff', 'applicants' => 12],
                    ['title' => 'Skilled Worker', 'applicants' => 9],
                ],
            ],
            'United Kingdom' => [
                'applicants' => 34,
                'positions' => [
                    ['title' => 'Care Worker', 'applicants' => 20],
                    ['title' => 'Hospitality Staff', 'applicants' => 14],
                ],
            ],
            'Germany' => [
                'applicants' => 19,
                'positions' => [
                    ['title' => 'Skilled Worker', 'applicants' => 11],
                    ['title' => 'Technician', 'applicants' => 8],
                ],
            ],
            'Italy' => [
                'applicants' => 16,
                'positions' => [
                    ['title' => 'Care Worker', 'applicants' => 10],
                    ['title' => 'Hospitality Staff', 'applicants' => 6],
                ],
            ],
            'Australia' => [
                'applicants' => 31,
                'positions' => [
                    ['title' => 'Farm Worker', 'applicants' => 17],
                    ['title' => 'Hospitality Staff', 'applicants' => 14],
                ],
            ],
            'New Zealand' => [
                'applicants' => 14,
                'positions' => [
                    ['title' => 'Farm Worker', 'applicants' => 8],
                    ['title' => 'Skilled Worker', 'applicants' => 6],
                ],
            ],
        ];

        $countries = [];

        foreach ($mockDeployments as $country => $deployment) {
            [$longitude, $latitude] = self::COUNTRY_COORDINATES[$country];

            $countries[] = [
                'country' => $country,
                'longitude' => $longitude,
                'latitude' => $latitude,
                'applicants' => $deployment['applicants'],
                'positions' => $deployment['positions'],
            ];
        }

        $data = [
            'is_mock' => true,
            'total_deployed' => array_sum(
                array_column($countries, 'applicants')
            ),
            'total_countries' => count($countries),
            'countries' => $countries,
        ];

        return response()->json([
            'status' => true,
            'message' => 'Mock deployment map data fetched successfully.',
            'data' => $data,
        ]);
    }
}
