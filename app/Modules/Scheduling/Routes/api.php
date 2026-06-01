<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Scheduling\Controllers\AppointmentController;

Route::prefix('api/v1')->middleware('api')->group(function () {
    Route::get('appointments', [AppointmentController::class, 'index']);
    Route::post('appointments', [AppointmentController::class, 'store']);
    Route::patch('appointments/{uuid}/cancel', [AppointmentController::class, 'cancel']);
});
