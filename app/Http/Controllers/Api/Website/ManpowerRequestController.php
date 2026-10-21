<?php

namespace App\Http\Controllers\Api\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\StoreManpowerRequestRequest;
use App\Services\Management\ManpowerRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ManpowerRequestController extends Controller
{
    public function __construct(protected ManpowerRequestService $manpowerRequestService)
    {
    }

    // POST /api/website/manpower-requests  (public)
    public function store(StoreManpowerRequestRequest $request): JsonResponse
    {
        Log::info('ManpowerRequest incoming', [
        'raw'       => $request->except('contact_fax'),
        'validated' => $request->validated(),
    ]);
        $result = $this->manpowerRequestService->store($request->validated());

        if (! $result['status']) {
            return response()->json($result, 400);
        }

        // Reference lang ang ibinabalik sa publiko
        return response()->json([
            'status'  => true,
            'message' => 'Manpower request submitted successfully',
            'data'    => ['reference' => $result['data']->reference],
        ], 201);
    }
}
