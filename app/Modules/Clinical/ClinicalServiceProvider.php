<?php

declare(strict_types=1);

namespace App\Modules\Clinical;

use App\Modules\Clinical\Repositories\ConsultationRepository;
use App\Modules\Clinical\Repositories\ConsultationRepositoryInterface;
use App\Modules\Clinical\Repositories\PrescriptionRepository;
use App\Modules\Clinical\Repositories\PrescriptionRepositoryInterface;
use App\Modules\Clinical\Repositories\LabOrderRepository;
use App\Modules\Clinical\Repositories\LabOrderRepositoryInterface;
use App\Modules\Clinical\Repositories\ImagingOrderRepository;
use App\Modules\Clinical\Repositories\ImagingOrderRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class ClinicalServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ConsultationRepositoryInterface::class,
            ConsultationRepository::class
        );

        $this->app->bind(
            ImagingOrderRepositoryInterface::class,
            ImagingOrderRepository::class
        );

        $this->app->bind(
            LabOrderRepositoryInterface::class,
            LabOrderRepository::class
        );

        $this->app->bind(
            PrescriptionRepositoryInterface::class,
            PrescriptionRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}