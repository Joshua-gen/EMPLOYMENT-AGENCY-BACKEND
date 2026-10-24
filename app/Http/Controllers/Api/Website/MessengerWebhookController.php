<?php

namespace App\Http\Controllers\Api\Website;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use JsonException;

class MessengerWebhookController
{
    /**
     * Verify the webhook subscription requested by Meta.
     */
    public function verify(Request $request): Response
    {
        $mode = $request->query('hub_mode')
            ?? $request->query('hub.mode');

        $token = $request->query('hub_verify_token')
            ?? $request->query('hub.verify_token');

        $challenge = $request->query('hub_challenge')
            ?? $request->query('hub.challenge');

        $verifyToken = config('services.messenger.verify_token');

        if (! is_string($verifyToken) || $verifyToken === '') {
            abort(503, 'Webhook verification is not configured.');
        }

        if (
            $mode !== 'subscribe' ||
            ! is_string($token) ||
            ! hash_equals($verifyToken, $token) ||
            ! is_string($challenge) ||
            $challenge === ''
        ) {
            abort(403, 'Webhook verification failed.');
        }

        return response($challenge, 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Receive and authenticate Messenger webhook events.
     */
    // public function handle(Request $request): JsonResponse
    // {
    //     $appSecret = config('services.messenger.app_secret');

    //     if (! is_string($appSecret) || $appSecret === '') {
    //         return response()->json([
    //             'message' => 'Webhook is not configured.',
    //         ], 503);
    //     }

    //     $signature = $request->header('X-Hub-Signature-256');
    //     $rawBody = $request->getContent();

    //     if (
    //         ! is_string($signature) ||
    //         ! str_starts_with($signature, 'sha256=')
    //     ) {
    //         return response()->json([
    //             'message' => 'Invalid webhook signature.',
    //         ], 401);
    //     }

    //     $expectedSignature = 'sha256=' . hash_hmac(
    //         'sha256',
    //         $rawBody,
    //         $appSecret
    //     );

    //     if (! hash_equals($expectedSignature, $signature)) {
    //         return response()->json([
    //             'message' => 'Invalid webhook signature.',
    //         ], 401);
    //     }

    //     try {
    //         $payload = json_decode(
    //             $rawBody,
    //             true,
    //             512,
    //             JSON_THROW_ON_ERROR
    //         );
    //     } catch (JsonException) {
    //         return response()->json([
    //             'message' => 'Invalid JSON payload.',
    //         ], 400);
    //     }

    //     if (
    //         ! is_array($payload) ||
    //         ($payload['object'] ?? null) !== 'page' ||
    //         ! isset($payload['entry']) ||
    //         ! is_array($payload['entry'])
    //     ) {
    //         return response()->json([
    //             'message' => 'Invalid Messenger event payload.',
    //         ], 400);
    //     }

    //     // Log metadata only. Never log message content or access tokens.
    //     Log::info('Messenger webhook received.', [
    //         'object' => $payload['object'],
    //         'entry_count' => count($payload['entry']),
    //     ]);

    //     // Event processing and replies will be added in a later phase.
    //     return response()->json([
    //         'received' => true,
    //     ]);
    // }


public function handle(Request $request): JsonResponse
{
    $appSecret = config('services.messenger.app_secret');

    if (! is_string($appSecret) || $appSecret === '') {
        return response()->json([
            'message' => 'Webhook is not configured.',
        ], 503);
    }

    $signature = $request->header('X-Hub-Signature-256');
    $rawBody = $request->getContent();

    if (
        ! is_string($signature) ||
        ! str_starts_with($signature, 'sha256=')
    ) {
        return response()->json([
            'message' => 'Invalid webhook signature.',
        ], 401);
    }

    $expectedSignature = 'sha256=' . hash_hmac(
        'sha256',
        $rawBody,
        $appSecret
    );

    if (! hash_equals($expectedSignature, $signature)) {
        return response()->json([
            'message' => 'Invalid webhook signature.',
        ], 401);
    }

    try {
        $payload = json_decode(
            $rawBody,
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    } catch (JsonException) {
        return response()->json([
            'message' => 'Invalid JSON payload.',
        ], 400);
    }

    if (
        ! is_array($payload) ||
        ($payload['object'] ?? null) !== 'page' ||
        ! isset($payload['entry']) ||
        ! is_array($payload['entry'])
    ) {
        return response()->json([
            'message' => 'Invalid Messenger event payload.',
        ], 400);
    }

    $textMessageCount = 0;
    $ignoredEventCount = 0;

    foreach ($payload['entry'] as $entry) {
        if (! is_array($entry)) {
            return response()->json([
                'message' => 'Invalid entry structure.',
            ], 400);
        }

        $messagingEvents = $entry['messaging'] ?? [];

        if (! is_array($messagingEvents)) {
            return response()->json([
                'message' => 'Invalid messaging event structure.',
            ], 400);
        }

        foreach ($messagingEvents as $event) {
            if (! is_array($event)) {
                $ignoredEventCount++;
                continue;
            }

            $senderId = $event['sender']['id'] ?? null;
            $message = $event['message'] ?? null;

            if (
                ! is_string($senderId) ||
                ! is_array($message)
            ) {
                $ignoredEventCount++;
                continue;
            }

            if (($message['is_echo'] ?? false) === true) {
                $ignoredEventCount++;
                continue;
            }

            $messageText = $message['text'] ?? null;

            if (
                ! is_string($messageText) ||
                trim($messageText) === ''
            ) {
                $ignoredEventCount++;
                continue;
            }

            $textMessageCount++;
        }
    }

    Log::info('Messenger webhook processed.', [
        'entry_count' => count($payload['entry']),
        'text_message_count' => $textMessageCount,
        'ignored_event_count' => $ignoredEventCount,
    ]);

    return response()->json([
        'received' => true,
        'text_messages' => $textMessageCount,
        'ignored_events' => $ignoredEventCount,
    ]);
}

}
