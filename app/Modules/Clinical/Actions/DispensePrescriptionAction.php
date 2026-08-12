<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Actions;

use App\Modules\Clinical\Models\Prescription;
use App\Modules\Clinical\Repositories\PrescriptionRepositoryInterface;
use App\Shared\Enums\EmployeeRoleType;
use App\Shared\Enums\PrescriptionStatus;
use App\Modules\Hr\Models\Employee;
use Illuminate\Validation\ValidationException;

final class DispensePrescriptionAction
{
    public function __construct(
        private PrescriptionRepositoryInterface $repository,
    ) {
    }

    public function execute(
        string $prescriptionUuid,
        string $employeeUuid
    ): Prescription {
        $prescription = $this->repository->findByUuid(
            $prescriptionUuid,
            true
        );

        if (! $prescription) {
            throw ValidationException::withMessages([
                'prescription_uuid' =>
                    'The specified prescription does not exist.',
            ]);
        }

        $employee = Employee::query()
            ->whereKey($employeeUuid)
            ->where('is_active', true)
            ->first();

        if (! $employee) {
            throw ValidationException::withMessages([
                'employee_uuid' =>
                    'The specified employee does not exist or is inactive.',
            ]);
        }

        if (! $prescription->isValid()) {
            throw ValidationException::withMessages([
                'prescription_uuid' =>
                    'The prescription is no longer valid for dispensing.',
            ]);
        }

        if ($prescription->items->isEmpty()) {
            throw ValidationException::withMessages([
                'prescription_uuid' =>
                    'The prescription contains no medication items.',
            ]);
        }

        $prescription->dispense($employee);

        return $prescription->fresh([
            'patient',
            'doctor',
            'dispensedBy',
            'items.medication.category',
        ]);
    }
}