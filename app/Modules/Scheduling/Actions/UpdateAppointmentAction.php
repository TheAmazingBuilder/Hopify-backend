<?php

declare(strict_types=1);

namespace App\Modules\Scheduling\Actions;

use App\Modules\Scheduling\DTOs\CreateAppointmentDTO;
use App\Modules\Scheduling\Models\Appointment;
use App\Modules\Scheduling\Repositories\AppointmentRepositoryInterface;
use App\Modules\Scheduling\Repositories\DoctorScheduleRepositoryInterface;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class UpdateAppointmentAction
{
    public function __construct(
        protected AppointmentRepositoryInterface $appointmentRepository,
        protected DoctorScheduleRepositoryInterface $scheduleRepository,
    ) {}

    public function execute(string $uuid, CreateAppointmentDTO $dto): Appointment
    {
        $appointment = $this->appointmentRepository->findByUuid($uuid);
        if (! $appointment) {
            throw ValidationException::withMessages([
                'appointment' => ['Rendez-vous introuvable.'],
            ]);
        }

        if ($appointment->isCancelled()) {
            throw ValidationException::withMessages([
                'appointment' => ['Impossible de modifier un rendez-vous annulé.'],
            ]);
        }

        if ($dto->end_time->lessThanOrEqualTo($dto->start_time)) {
            throw ValidationException::withMessages([
                'end_time' => ['L'heure de fin doit être après l'heure de début.'],
            ]);
        }

        $overlapping = $this->appointmentRepository->getOverlapping(
            $dto->doctor_uuid,
            $dto->start_time->toDateTimeString(),
            $dto->end_time->toDateTimeString(),
            $uuid
        );

        if ($overlapping) {
            throw ValidationException::withMessages([
                'start_time' => ['Ce créneau chevauche un rendez-vous existant.'],
            ]);
        }

        $dayOfWeek = (int) $dto->start_time->format('w');
        $schedules = $this->scheduleRepository->getByDoctorAndDay($dto->doctor_uuid, $dayOfWeek);

        if ($schedules->isEmpty()) {
            throw ValidationException::withMessages([
                'start_time' => ['Le médecin n'est pas disponible ce jour-là.'],
            ]);
        }

        $startTime = Carbon::createFromFormat('H:i:s', $dto->start_time->format('H:i:s'));
        $endTime = Carbon::createFromFormat('H:i:s', $dto->end_time->format('H:i:s'));

        $isWithinSchedule = $schedules->some(function ($schedule) use ($startTime, $endTime) {
            $scheduleStart = Carbon::createFromFormat('H:i:s', $schedule->start_time->format('H:i:s'));
            $scheduleEnd = Carbon::createFromFormat('H:i:s', $schedule->end_time->format('H:i:s'));
            return $startTime->greaterThanOrEqualTo($scheduleStart)
                && $endTime->lessThanOrEqualTo($scheduleEnd);
        });

        if (! $isWithinSchedule) {
            throw ValidationException::withMessages([
                'start_time' => ['Le rendez-vous n'est pas dans les horaires du médecin.'],
            ]);
        }

        $this->appointmentRepository->update($uuid, $dto->toArray());
        return $appointment->fresh();
    }
}
