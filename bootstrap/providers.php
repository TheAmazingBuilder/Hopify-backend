<?php

use App\Providers\AppServiceProvider;
use App\Providers\TenancyServiceProvider;
use App\Modules\Patient\PatientServiceProvider;
use App\Modules\Scheduling\SchedulingServiceProvider;
use App\Modules\Hr\HrServiceProvider;
use App\Modules\Doctor\DoctorServiceProvider;

return [
    AppServiceProvider::class,
    TenancyServiceProvider::class,
    PatientServiceProvider::class,
    SchedulingServiceProvider::class,
];
