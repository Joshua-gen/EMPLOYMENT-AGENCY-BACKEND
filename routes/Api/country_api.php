<?php

use App\Http\Controllers\Api\SystemDef\CountryController;
use Illuminate\Support\Facades\Route;

Route::prefix('sysdef')->group(function () {
    Route::apiResource('countries', CountryController::class)->parameters(['countries' => 'id']);
});
