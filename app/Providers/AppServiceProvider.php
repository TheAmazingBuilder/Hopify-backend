<?php

declare(strict_types=1);

namespace App\Providers;

use App\Modules\Clinical\Models\Consultation;
use App\Modules\Clinical\Models\ImagingOrder;
use App\Modules\Clinical\Models\LabOrder;
use App\Modules\Clinical\Models\Prescription;
use App\Modules\Clinical\Policies\ConsultationPolicy;
use App\Modules\Clinical\Policies\ImagingOrderPolicy;
use App\Modules\Clinical\Policies\LabOrderPolicy;
use App\Modules\Clinical\Policies\PrescriptionPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Model::shouldBeStrict(! $this->app->isProduction());
    }

    public function boot(): void
    {
        Gate::policy(
            Consultation::class,
            ConsultationPolicy::class
        );

        Gate::policy(
            Prescription::class,
            PrescriptionPolicy::class
        );

        Gate::policy(
            LabOrder::class,
            LabOrderPolicy::class
        );

        Gate::policy(
            ImagingOrder::class,
            ImagingOrderPolicy::class
        );
    }
}