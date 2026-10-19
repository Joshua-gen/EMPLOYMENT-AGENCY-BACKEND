<?php

use App\Http\Controllers\Api\Management\JobController;
use Illuminate\Support\Facades\Route;

Route::prefix('management')->group(function () {
    Route::get('jobs/summary', [JobController::class, 'summary']); // dapat nasa taas ng apiResource
    Route::apiResource('jobs', JobController::class)->parameters(['jobs' => 'id']);
});
