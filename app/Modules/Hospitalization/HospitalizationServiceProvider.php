<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization;

use Illuminate\Support\ServiceProvider;
use App\Modules\Hospitalization\Repositories\RoomRepositoryInterface;
use App\Modules\Hospitalization\Repositories\RoomRepository;
use App\Modules\Hospitalization\Repositories\BedRepositoryInterface;
use App\Modules\Hospitalization\Repositories\BedRepository;
use App\Modules\Hospitalization\Repositories\HospitalizationRepositoryInterface;
use App\Modules\Hospitalization\Repositories\HospitalizationRepository;
use App\Modules\Hospitalization\Repositories\NursingNoteRepositoryInterface;
use App\Modules\Hospitalization\Repositories\NursingNoteRepository;
use App\Modules\Hospitalization\Repositories\DoctorRoundRepositoryInterface;
use App\Modules\Hospitalization\Repositories\DoctorRoundRepository;
use App\Modules\Hospitalization\Repositories\BedAssignmentRepositoryInterface;
use App\Modules\Hospitalization\Repositories\BedAssignmentRepository;

class HospitalizationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RoomRepositoryInterface::class, RoomRepository::class);
        $this->app->bind(BedRepositoryInterface::class, BedRepository::class);
        $this->app->bind(HospitalizationRepositoryInterface::class, HospitalizationRepository::class);
        $this->app->bind(NursingNoteRepositoryInterface::class, NursingNoteRepository::class);
        $this->app->bind(DoctorRoundRepositoryInterface::class, DoctorRoundRepository::class);
        $this->app->bind(BedAssignmentRepositoryInterface::class, BedAssignmentRepository::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');
    }
}
