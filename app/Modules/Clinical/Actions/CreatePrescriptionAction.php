<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Actions;

use App\Modules\Clinical\DTOs\CreatePrescriptionDTO;
use App\Modules\Clinical\Models\Prescription;
//use App\Modules\Clinical\Models\PrescriptionItem;
use App\Modules\Clinical\Models\Medication;
use App\Modules\Clinical\Repositories\PrescriptionRepositoryInterface;
//use App\Modules\Clinical\Repositories\ConsultationRepositoryInterface;
//use App\Modules\Foundation\Models\User;
use App\Modules\Hr\Models\Employee;
use App\Modules\Patient\Models\Patient;
use App\Shared\Enums\PrescriptionStatus;
use App\Modules\Clinical\Models\Consultation;
use App\Shared\Enums\EmployeeRoleType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

final class CreatePrescriptionAction
{
    public function __construct(
        private PrescriptionRepositoryInterface $repository,
    ) {
    }

    public function execute(
        CreatePrescriptionDTO $dto
    ): Prescription {
        $this->validatePatient($dto->patientUuid);

        $this->validateDoctor($dto->doctorUuid);

        $consultation = $this->validateConsultation(
            $dto
        );

        $this->validateItems(
            $dto->items
        );

        return DB::transaction(
            function () use ($dto, $consultation): Prescription {
                $prescription = $this->repository->create([
                    'consultation_uuid' => $dto->consultationUuid,
                    'patient_uuid' => $dto->patientUuid,
                    'doctor_uuid' => $dto->doctorUuid,
                    'prescription_number' =>
                        $dto->prescriptionNumber
                        ?? $this->generatePrescriptionNumber(),
                    'status' => PrescriptionStatus::Active,
                    'notes' => $dto->notes,
                    'valid_until' => $dto->validUntil,
                ]);

                foreach ($dto->items as $item) {
                    $this->repository->createItem([
                        'prescription_uuid' => $prescription->uuid,
                        'medication_uuid' => $item['medication_uuid'],
                        'dosage' => $item['dosage'],
                        'frequency' => $item['frequency'],
                        'route' => $item['route'] ?? null,
                        'duration_days' => $item['duration_days'] ?? null,
                        'quantity' => $item['quantity'] ?? null,
                        'instructions' => $item['instructions'] ?? null,
                        'is_substitutable' =>
                            $item['is_substitutable'] ?? true,
                    ]);
                }

                return $prescription->load([
                    'patient',
                    'doctor',
                    'consultation',
                    'items.medication.category',
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

    private function validateDoctor(
        string $doctorUuid
    ): void {
        $exists = Employee::query()
            ->whereKey($doctorUuid)
            ->where(
                'role_type',
                EmployeeRoleType::Doctor->value
            )
            ->where('is_active', true)
            ->exists();

        if (! $exists) {
            throw ValidationException::withMessages([
                'doctor_uuid' =>
                    'The specified employee is not an active doctor.',
            ]);
        }
    }

    private function validateConsultation(
        CreatePrescriptionDTO $dto
    ): ?Consultation {
        if ($dto->consultationUuid === null) {
            return null;
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

        if (
            (string) $consultation->doctor_uuid
            !== $dto->doctorUuid
        ) {
            throw ValidationException::withMessages([
                'consultation_uuid' =>
                    'The consultation is not assigned to the specified doctor.',
            ]);
        }

        return $consultation;
    }

    private function validateItems(
        array $items
    ): void {
        if ($items === []) {
            throw ValidationException::withMessages([
                'items' =>
                    'A prescription must contain at least one medication.',
            ]);
        }

        $medicationUuids = array_column(
            $items,
            'medication_uuid'
        );

        $medications = Medication::query()
            ->whereIn('uuid', $medicationUuids)
            ->get()
            ->keyBy('uuid');

        foreach ($items as $index => $item) {
            $medicationUuid = $item['medication_uuid'] ?? null;

            if (
                ! $medicationUuid
                || ! isset($medications[$medicationUuid])
            ) {
                throw ValidationException::withMessages([
                    "items.{$index}.medication_uuid" =>
                        'The specified medication does not exist.',
                ]);
            }

            /** @var Medication $medication */
            $medication = $medications[$medicationUuid];

            if (! $medication->is_active) {
                throw ValidationException::withMessages([
                    "items.{$index}.medication_uuid" =>
                        "Medication '{$medication->name}' is inactive.",
                ]);
            }

            if (! $medication->requires_prescription) {
                throw ValidationException::withMessages([
                    "items.{$index}.medication_uuid" =>
                        "Medication '{$medication->name}' does not require a prescription.",
                ]);
            }
        }
    }

    private function generatePrescriptionNumber(): string
    {
        do {
            $number = sprintf(
                'RX-%s-%s',
                now()->format('Ym'),
                strtoupper(Str::random(6))
            );
        } while (
            Prescription::query()
                ->where('prescription_number', $number)
                ->exists()
        );

        return $number;
    }
}