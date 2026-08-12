<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Actions;

use App\Modules\Clinical\DTOs\CreateLabOrderDTO;
use App\Modules\Clinical\Models\LabOrder;
use App\Modules\Clinical\Models\LabTest;
use App\Modules\Clinical\Repositories\LabOrderRepositoryInterface;
use App\Modules\Clinical\Models\Consultation;
use App\Modules\Hr\Models\Employee;
use App\Modules\Patient\Models\Patient;
use App\Shared\Enums\EmployeeRoleType;
use App\Shared\Enums\LabOrderItemStatus;
use App\Shared\Enums\LabOrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class CreateLabOrderAction
{
    public function __construct(
        private LabOrderRepositoryInterface $repository,
    ) {
    }

    public function execute(
        CreateLabOrderDTO $dto
    ): LabOrder {
        $this->validatePatient($dto->patientUuid);

        $this->validateOrderingEmployee(
            $dto->orderedByUuid
        );

        $this->validateConsultation(
            $dto
        );

        $labTests = $this->validateLabTests(
            $dto->labTestUuids
        );

        return DB::transaction(
            function () use (
                $dto,
                $labTests
            ): LabOrder {
                $order = $this->repository->create([
                    'consultation_uuid' => $dto->consultationUuid,
                    'patient_uuid' => $dto->patientUuid,
                    'ordered_by_uuid' => $dto->orderedByUuid,
                    'order_number' => $dto->orderNumber
                        ?? $this->generateOrderNumber(),
                    'status' => LabOrderStatus::Pending,
                    'priority' => $dto->priority,
                    'clinical_notes' => $dto->clinicalNotes,
                ]);

                foreach ($labTests as $labTest) {
                    $this->repository->createItem([
                        'lab_order_uuid' => $order->uuid,
                        'lab_test_uuid' => $labTest->uuid,
                        'status' => LabOrderItemStatus::Pending,
                    ]);
                }

                return $order->load([
                    'patient',
                    'orderedBy',
                    'consultation',
                    'items.labTest',
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
        $employee = Employee::query()
            ->whereKey($employeeUuid)
            ->where('is_active', true)
            ->first();

        if (! $employee) {
            throw ValidationException::withMessages([
                'ordered_by_uuid' =>
                    'The specified employee does not exist or is inactive.',
            ]);
        }

        /*
         * Nous n'imposons pas "doctor" ici.
         *
         * Un ordre labo pourrait éventuellement être créé
         * par différents professionnels selon les permissions
         * du système hospitalier.
         */
    }

    private function validateConsultation(
        CreateLabOrderDTO $dto
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

    private function validateLabTests(
        array $labTestUuids
    ) {
        if ($labTestUuids === []) {
            throw ValidationException::withMessages([
                'lab_test_uuids' =>
                    'At least one laboratory test is required.',
            ]);
        }

        $labTestUuids = array_values(
            array_unique($labTestUuids)
        );

        $labTests = LabTest::query()
            ->whereIn('uuid', $labTestUuids)
            ->where('is_active', true)
            ->get();

        if ($labTests->count() !== count($labTestUuids)) {
            throw ValidationException::withMessages([
                'lab_test_uuids' =>
                    'One or more laboratory tests do not exist or are inactive.',
            ]);
        }

        return $labTests;
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = sprintf(
                'LAB-%s-%s',
                now()->format('Ym'),
                strtoupper(Str::random(6))
            );
        } while (
            LabOrder::query()
                ->where('order_number', $number)
                ->exists()
        );

        return $number;
    }
}