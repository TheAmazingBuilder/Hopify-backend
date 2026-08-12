<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Actions;

use App\Modules\Clinical\DTOs\CreateImagingOrderDTO;
use App\Modules\Clinical\Models\Consultation;
use App\Modules\Clinical\Models\ImagingOrder;
use App\Modules\Clinical\Repositories\ImagingOrderRepositoryInterface;
use App\Modules\Hr\Models\Employee;
use App\Modules\Patient\Models\Patient;
use App\Shared\Enums\EmployeeRoleType;
use App\Shared\Enums\ImagingModality;
use App\Shared\Enums\ImagingOrderStatus;
use App\Shared\Enums\ImagingUrgency;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CreateImagingOrderAction
{
    public function __construct(
        private ImagingOrderRepositoryInterface $repository,
    ) {
    }

    public function execute(
        CreateImagingOrderDTO $dto
    ): ImagingOrder {
        $this->validatePatient($dto->patientUuid);

        $this->validateOrderingEmployee(
            $dto->orderedByUuid
        );

        $this->validateModality($dto->modality);

        $this->validateUrgency($dto->urgency);

        $this->validateConsultation($dto);

        return DB::transaction(
            function () use ($dto): ImagingOrder {
                $order = $this->repository->create([
                    'consultation_uuid' => $dto->consultationUuid,
                    'patient_uuid' => $dto->patientUuid,
                    'ordered_by_uuid' => $dto->orderedByUuid,
                    'modality' => $dto->modality,
                    'body_part' => $dto->bodyPart,
                    'urgency' => $dto->urgency,
                    'status' => ImagingOrderStatus::Pending,
                    'clinical_indication' => $dto->clinicalIndication,
                    'notes' => $dto->notes,
                ]);

                return $order->load([
                    'patient',
                    'orderedBy',
                    'consultation',
                ]);
            }
        );
    }

    private function validatePatient(
        string $patientUuid
    ): void {
        if (! Patient::query()->whereKey($patientUuid)->exists()) {
            throw ValidationException::withMessages([
                'patient_uuid' =>
                    'The specified patient does not exist.',
            ]);
        }
    }

    private function validateOrderingEmployee(
        string $employeeUuid
    ): void {
        $exists = Employee::query()
            ->whereKey($employeeUuid)
            ->where('is_active', true)
            ->exists();

        if (! $exists) {
            throw ValidationException::withMessages([
                'ordered_by_uuid' =>
                    'The specified employee does not exist or is inactive.',
            ]);
        }
    }

    private function validateModality(
        string $modality
    ): void {
        $validValues = array_column(
            ImagingModality::cases(),
            'value'
        );

        if (! in_array($modality, $validValues, true)) {
            throw ValidationException::withMessages([
                'modality' =>
                    'The specified imaging modality is invalid.',
            ]);
        }
    }

    private function validateUrgency(
        string $urgency
    ): void {
        $validValues = array_column(
            ImagingUrgency::cases(),
            'value'
        );

        if (! in_array($urgency, $validValues, true)) {
            throw ValidationException::withMessages([
                'urgency' =>
                    'The specified imaging urgency is invalid.',
            ]);
        }
    }

    private function validateConsultation(
        CreateImagingOrderDTO $dto
    ): void {
        if ($dto->consultationUuid === null) {
            return;
        }

        $consultation = Consultation::query()
            ->find($dto->consultationUuid);

        if (! $consultation) {
            throw ValidationException::withMessages([
                'consultation_uuid' =>
                    'The specified consultation does not exist.',
            ]);
        }

        if (
            (string) $consultation->patient_uuid
            !== $dto->patientUuid
        ) {
            throw ValidationException::withMessages([
                'consultation_uuid' =>
                    'The consultation does not belong to the specified patient.',
            ]);
        }
    }
}