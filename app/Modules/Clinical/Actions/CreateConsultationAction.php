<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Actions;

use App\Modules\Clinical\DTOs\CreateConsultationDTO;
use App\Modules\Clinical\Models\Consultation;
use App\Modules\Clinical\Repositories\ConsultationRepositoryInterface;
use App\Modules\Hr\Models\Employee;
use App\Modules\Hospitalization\Models\Hospitalization;
use App\Modules\Patient\Models\Patient;
use App\Modules\Scheduling\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CreateConsultationAction
{
    public function __construct(
        private ConsultationRepositoryInterface $repository,
    ) {
    }

    public function execute(
        CreateConsultationDTO $dto
    ): Consultation {
        $this->validateReferences($dto);

        return DB::transaction(function () use ($dto): Consultation {
            $consultation = $this->repository->create(
                $dto->toArray()
            );

            return $consultation->load([
                'patient',
                'doctor',
                'appointment',
                'hospitalization',
            ]);
        });
    }

    private function validateReferences(
        CreateConsultationDTO $dto
    ): void {
        $patient = Patient::query()
            ->find($dto->patientUuid);

        if (! $patient) {
            throw ValidationException::withMessages([
                'patient_uuid' => 'The specified patient does not exist.',
            ]);
        }

        $doctor = Employee::query()
            ->whereKey($dto->doctorUuid)
            ->where('role_type', 'doctor')
            ->where('is_active', true)
            ->first();

        if (! $doctor) {
            throw ValidationException::withMessages([
                'doctor_uuid' => 'The specified employee is not an active doctor.',
            ]);
        }

        if ($dto->appointmentUuid !== null) {
            $appointment = Appointment::query()
                ->find($dto->appointmentUuid);

            if (! $appointment) {
                throw ValidationException::withMessages([
                    'appointment_uuid' => 'The specified appointment does not exist.',
                ]);
            }

            if ((string) $appointment->patient_uuid !== $dto->patientUuid) {
                throw ValidationException::withMessages([
                    'appointment_uuid' => 'The appointment does not belong to the specified patient.',
                ]);
            }

            if ((string) $appointment->doctor_uuid !== $dto->doctorUuid) {
                throw ValidationException::withMessages([
                    'appointment_uuid' => 'The appointment is not assigned to the specified doctor.',
                ]);
            }
        }

        if ($dto->hospitalizationUuid !== null) {
            $hospitalization = Hospitalization::query()
                ->find($dto->hospitalizationUuid);

            if (! $hospitalization) {
                throw ValidationException::withMessages([
                    'hospitalization_uuid' => 'The specified hospitalization does not exist.',
                ]);
            }

            if (
                (string) $hospitalization->patient_uuid
                !== $dto->patientUuid
            ) {
                throw ValidationException::withMessages([
                    'hospitalization_uuid' => 'The hospitalization does not belong to the specified patient.',
                ]);
            }
        }
    }
}