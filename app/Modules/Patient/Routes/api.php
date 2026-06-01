<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Patient\Controllers\PatientController;

Route::prefix('api/v1')->middleware('api')->group(function () {
    Route::apiResource('patients', PatientController::class);
});
