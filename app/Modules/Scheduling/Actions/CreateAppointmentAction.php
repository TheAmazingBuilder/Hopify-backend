<?php

namespace App\Modules\Scheduling\Actions;

use App\Modules\Scheduling\Models\Appointment;
use App\Modules\Scheduling\Repositories\AppointmentRepositoryInterface;
use App\Modules\Scheduling\DTOs\CreateAppointmentDTO;
use App\Modules\Scheduling\Services\SchedulingService;
use App\Shared\Exceptions\AppointmentConflictException;
use App\Modules\Foundation\Models\AuditLog;

class CreateAppointmentAction
{
    public function __construct(
        protected AppointmentRepositoryInterface $repository,
        protected SchedulingService $schedulingService
    ) {}

    public function execute(CreateAppointmentDTO $dto): Appointment
    {
        // Vérification des conflits (Logique métier avancée)
        $isAvailable = $this->schedulingService->isSlotAvailable(
            $dto->doctor_uuid,
            $dto->start_time,
            $dto->end_time
        );

        if (!$isAvailable) {
            throw new AppointmentConflictException();
        }
        
        $appointment = $this->repository->create($dto->toArray());
        
        AuditLog::record('appointment.created', $appointment);
        
        return $appointment;
    }
}
