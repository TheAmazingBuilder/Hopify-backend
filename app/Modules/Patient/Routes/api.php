<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Modules\Patient\Controllers\PatientController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*Route::prefix('api/v1')->middleware('api')->group(function () {
    Route::apiResource('patients', PatientController::class);
});
*/

Route::middleware(['api','auth:sanctum',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,])->prefix('api/v1')->group(function () {
    Route::apiResource('patients', PatientController::class);
});
