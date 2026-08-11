<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Scheduling\Controllers\AppointmentController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;


Route::middleware(['api','auth:sanctum',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->prefix('api/v1')->group(function () {
    Route::get('appointments', [AppointmentController::class, 'index']);
    Route::post('appointments', [AppointmentController::class, 'store']);
    Route::patch('appointments/{uuid}/cancel', [AppointmentController::class, 'cancel']);
});
