
<?php

use App\Http\Controllers\Api\Management\ApplicantController;
use App\Http\Controllers\Api\Management\EmployerController;
use App\Http\Controllers\Api\Management\JobController;
use App\Http\Controllers\Api\Website\ApplicationController;
use App\Http\Controllers\Api\Management\BusinessCategoryController;
use App\Http\Controllers\Api\Website\JobController as WebsiteJobController;
use App\Http\Controllers\Api\Management\ManpowerRequestController;
use App\Http\Controllers\Api\Website\ManpowerRequestController as WebsiteManpowerRequestController;
use App\Http\Controllers\Api\Website\DeploymentMapController;
use Illuminate\Support\Facades\Route;

// ========================================
// PUBLIC WEBSITE API
// No auth:sanctum required
// ========================================
Route::prefix('website')->group(function () {
    Route::get('jobs', [WebsiteJobController::class, 'index']);

    Route::get('jobs/{id}', [WebsiteJobController::class, 'show'])
        ->whereNumber('id');

    Route::post('applications', [ApplicationController::class, 'store'])
        ->middleware('throttle:10,1');

    Route::get('business-categories', [BusinessCategoryController::class, 'index']);

    Route::post('manpower-requests', [WebsiteManpowerRequestController::class, 'store'])->middleware('throttle:5,10');
    Route::get('deployment-map', [DeploymentMapController::class, 'index']
);
});

// ========================================
// PROTECTED MANAGEMENT API
// Requires Sanctum authentication
// ========================================
Route::middleware('auth:sanctum')
    ->prefix('management')
    ->group(function () {
        // Jobs
        Route::get('jobs/summary', [JobController::class, 'summary']);

        Route::apiResource('jobs', JobController::class)
            ->parameters(['jobs' => 'id']);

        // Employers
        Route::apiResource('employers', EmployerController::class)
            ->parameters(['employers' => 'id']);

        // Applicants
        Route::get('applicants/summary', [ApplicantController::class, 'summary']);

        Route::apiResource('applicants', ApplicantController::class)
            ->only(['index', 'show', 'store', 'update'])
            ->parameters(['applicants' => 'id']);

        Route::patch('applicants/{id}/assign-job', [ApplicantController::class, 'assignJob']);

        Route::patch('applicants/{id}/assign-employer', [ApplicantController::class, 'assignEmployer']);

        Route::get('business-categories', [BusinessCategoryController::class, 'index']);

        Route::get('manpower-requests/summary', [ManpowerRequestController::class, 'summary']);
        Route::apiResource('manpower-requests', ManpowerRequestController::class)
            ->only(['index', 'show'])
            ->parameters(['manpower-requests' => 'id']);
        Route::patch('manpower-requests/{id}/approve', [ManpowerRequestController::class, 'approve']);
        Route::patch('manpower-requests/{id}/reject', [ManpowerRequestController::class, 'reject']);
        Route::patch('manpower-requests/{id}/positions/{position}/link-job', [ManpowerRequestController::class, 'linkJob']);
        Route::delete('manpower-requests/{id}/positions/{position}/link-job', [ManpowerRequestController::class, 'unlinkJob']);
    });

// ========================================
// SIGNED DOCUMENT DOWNLOAD
// Requires a valid signed URL, not Sanctum
// ========================================
Route::get('applicant-documents/{document}', [ApplicantController::class, 'download'])
    ->name('applicant-documents.show')
    ->middleware('signed');
