
<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// Public API routes
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1');

// Auto-load module routes OUTSIDE auth:sanctum.
// Public website routes will work without authentication.
foreach (glob(__DIR__ . '/Api/*.php') as $file) {
    require $file;
}

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
});
