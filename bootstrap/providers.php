<?php

use App\Providers\AppServiceProvider;
use App\Providers\TenancyServiceProvider;
use App\Modules\Patient\PatientServiceProvider;
use App\Modules\Scheduling\SchedulingServiceProvider;
use App\Modules\Hr\HrServiceProvider;
use App\Modules\Doctor\DoctorServiceProvider;
use App\Modules\Clinical\ClinicalServiceProvider;

return [
    AppServiceProvider::class,
    TenancyServiceProvider::class,
    PatientServiceProvider::class,
    SchedulingServiceProvider::class,
    HrServiceProvider::class,
    ClinicalServiceProvider::class,
];
