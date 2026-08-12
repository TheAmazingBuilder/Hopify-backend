<?php

namespace App\Modules\Scheduling;

use Illuminate\Support\ServiceProvider;
use App\Modules\Scheduling\Repositories\AppointmentRepositoryInterface;
use App\Modules\Scheduling\Repositories\AppointmentRepository;
use App\Modules\Scheduling\Repositories\DoctorScheduleRepositoryInterface;
use App\Modules\Scheduling\Repositories\DoctorScheduleRepository;

class SchedulingServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind du Repository
        $this->app->bind(AppointmentRepositoryInterface::class, AppointmentRepository::class);
        $this->app->bind(DoctorScheduleRepositoryInterface::class, DoctorScheduleRepository::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Chargement des routes du module
        if (file_exists(__DIR__ . '/Routes/api.php')) {
            $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');
        }
    }
}
