
<?php

use App\Http\Controllers\Api\Website\MessengerWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('webhooks/messenger')
    ->name('messenger.webhook.')
    ->group(function () {
        Route::get('/', [MessengerWebhookController::class, 'verify'])
            ->name('verify');

        Route::post('/', [MessengerWebhookController::class, 'handle'])
            ->name('handle')
            ->middleware('throttle:60,1');
    });
