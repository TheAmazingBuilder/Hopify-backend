<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Modules\Hospitalization\Controllers\RoomController;
use App\Modules\Hospitalization\Controllers\BedController;
use App\Modules\Hospitalization\Controllers\HospitalizationController;
use App\Modules\Hospitalization\Controllers\NursingNoteController;
use App\Modules\Hospitalization\Controllers\DoctorRoundController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'api',
    'auth:sanctum',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->prefix('api/v1')->group(function () {
    Route::apiResource('rooms', RoomController::class);

    Route::apiResource('beds', BedController::class);
    Route::get('beds/room/{roomUuid}/available', [BedController::class, 'availableByRoom']);

    Route::apiResource('hospitalizations', HospitalizationController::class);
    Route::post('hospitalizations/{uuid}/discharge', [HospitalizationController::class, 'discharge']);
    Route::post('hospitalizations/{uuid}/transfer', [HospitalizationController::class, 'transfer']);

    Route::get('hospitalizations/{hospitalizationUuid}/nursing-notes', [NursingNoteController::class, 'index']);
    Route::post('hospitalizations/{hospitalizationUuid}/nursing-notes', [NursingNoteController::class, 'store']);
    Route::get('nursing-notes/{uuid}', [NursingNoteController::class, 'show']);
    Route::put('nursing-notes/{uuid}', [NursingNoteController::class, 'update']);
    Route::delete('nursing-notes/{uuid}', [NursingNoteController::class, 'destroy']);

    Route::get('hospitalizations/{hospitalizationUuid}/doctor-rounds', [DoctorRoundController::class, 'index']);
    Route::post('hospitalizations/{hospitalizationUuid}/doctor-rounds', [DoctorRoundController::class, 'store']);
    Route::get('doctor-rounds/{uuid}', [DoctorRoundController::class, 'show']);
    Route::put('doctor-rounds/{uuid}', [DoctorRoundController::class, 'update']);
    Route::delete('doctor-rounds/{uuid}', [DoctorRoundController::class, 'destroy']);
});
