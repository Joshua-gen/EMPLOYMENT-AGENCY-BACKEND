<?php

namespace App\Http\Controllers\Api\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\StoreApplicationRequest;
use App\Services\Management\ApplicantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApplicationController extends Controller
{
    public function __construct(protected ApplicantService $applicantService)
    {
    }

    // POST /api/applications  (multipart/form-data, public)
    public function store(StoreApplicationRequest $request): JsonResponse
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
        $data = $request->safe()->except(['resume', 'valid_id', 'passport', 'other_requirements', 'g_recaptcha_response',]);

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
