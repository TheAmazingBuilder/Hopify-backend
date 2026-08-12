<?php

declare(strict_types=1);

namespace App\Modules\Scheduling\Actions;

use App\Modules\Scheduling\DTOs\CreateDoctorScheduleDTO;
use App\Modules\Scheduling\Models\DoctorSchedule;
use App\Modules\Scheduling\Repositories\DoctorScheduleRepositoryInterface;
use Illuminate\Validation\ValidationException;

class CreateDoctorScheduleAction
{
    public function __construct(
        protected DoctorScheduleRepositoryInterface $repository
    ) {}

    public function execute(CreateDoctorScheduleDTO $dto): DoctorSchedule
    {
        if ($dto->end_time->lessThanOrEqualTo($dto->start_time)) {
            throw ValidationException::withMessages([
                'end_time' => ['L'heure de fin doit être après l'heure de début.'],
            ]);
        }

        if ($dto->day_of_week < 0 || $dto->day_of_week > 6) {
            throw ValidationException::withMessages([
                'day_of_week' => ['Le jour de la semaine doit être entre 0 (dimanche) et 6 (samedi).'],
            ]);
        }

        return $this->repository->create($dto->toArray());
    }
}
