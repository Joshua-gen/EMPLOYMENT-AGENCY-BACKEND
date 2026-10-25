<?php

namespace App\Http\Controllers\Api\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\StoreManpowerRequestRequest;
use App\Services\Management\ManpowerRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ManpowerRequestController extends Controller
{
    public function __construct(protected ManpowerRequestService $manpowerRequestService)
    {
    }

    // POST /api/website/manpower-requests  (public)
    public function store(StoreManpowerRequestRequest $request): JsonResponse
    {
        $token = $request->input('g_recaptcha_response');
        $secret = config('services.recaptcha.secret_key');

        if (! $secret || ! $token) {
            return response()->json([
                'message' => 'Please complete the reCAPTCHA verification.',
                'errors' => [
                    'g_recaptcha_response' => [
                        'CAPTCHA verification is required.',
                    ],
                ],
            ], 422);
        }

        try {
            $verification = Http::asForm()
                ->timeout(10)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $request->ip(),
                ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json([
                'message' => 'CAPTCHA verification is temporarily unavailable. Please try again.',
            ], 503);
        }

        if (! $verification->successful() || ! $verification->json('success')) {
            return response()->json([
                'message' => 'CAPTCHA verification failed. Please try again.',
                'errors' => [
                    'g_recaptcha_response' => [
                        'Please complete the CAPTCHA again.',
                    ],
                ],
            ], 422);
        }

        // Huwag ipasa ang CAPTCHA token o honeypot sa service.
        $data = $request->safe()->except([
            'g_recaptcha_response',
            'contact_fax',
        ]);

        $result = $this->manpowerRequestService->store($data);

        if (! $result['status']) {
            return response()->json($result, 400);
        }

        return response()->json([
            'status' => true,
            'message' => 'Manpower request submitted successfully',
            'data' => [
                'reference' => $result['data']->reference,
            ],
        ], 201);
    }

}
