<?php

namespace App\Http\Controllers\Api\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\StoreApplicationRequest;
use App\Services\Management\ApplicantService;
use Illuminate\Http\JsonResponse;

class ApplicationController extends Controller
{
    public function __construct(protected ApplicantService $applicantService)
    {
    }

    // POST /api/applications  (multipart/form-data, public)
    public function store(StoreApplicationRequest $request): JsonResponse
    {
        $data = $request->safe()->except(['resume', 'valid_id', 'passport', 'other_requirements']);

        $files = array_filter([
            'resume'   => $request->file('resume'),
            'valid_id' => $request->file('valid_id'),
            'passport' => $request->file('passport'),
            'other'    => $request->file('other_requirements'),
        ]);

        $result = $this->applicantService->store($data, $files);

        if (! $result['status']) {
            return response()->json($result, 400);
        }

        // Reference lang ang ibinabalik sa publiko
        return response()->json([
            'status'  => true,
            'message' => 'Application submitted successfully',
            'data'    => ['reference' => $result['data']->reference],
        ], 201);
    }
}
