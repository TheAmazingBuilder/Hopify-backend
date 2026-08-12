<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Actions;

use App\Modules\Clinical\Models\LabResult;
use App\Modules\Clinical\Repositories\LabOrderRepositoryInterface;
use App\Modules\Hr\Models\Employee;
use App\Shared\Enums\EmployeeRoleType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class ValidateLabResultAction
{
    public function __construct(
        private LabOrderRepositoryInterface $repository,
    ) {
    }

    public function execute(
        string $labResultUuid,
        string $employeeUuid
    ): LabResult {
        $result = LabResult::query()
            ->with([
                'labOrderItem.labOrder',
                'labOrderItem.labTest',
                'resultedBy',
            ])
            ->find($labResultUuid);

        if (! $result) {
            throw ValidationException::withMessages([
                'lab_result_uuid' =>
                    'The specified laboratory result does not exist.',
            ]);
        }

        if ($result->validated_at !== null) {
            throw ValidationException::withMessages([
                'lab_result_uuid' =>
                    'The laboratory result is already validated.',
            ]);
        }

        $validator = Employee::query()
            ->whereKey($employeeUuid)
            ->where('is_active', true)
            ->first();

        if (! $validator) {
            throw ValidationException::withMessages([
                'employee_uuid' =>
                    'The specified employee does not exist or is inactive.',
            ]);
        }

        /*
         * La permission exacte sera raffinée dans la Policy.
         *
         * Ici nous empêchons simplement une validation
         * par un employé inexistant/inactif.
         */

        return DB::transaction(
            function () use (
                $result,
                $validator
            ): LabResult {
                $this->repository->updateResult(
                    $result->uuid,
                    [
                        'validated_by_uuid' =>
                            $validator->uuid,
                        'validated_at' => now(),
                    ]
                );

                return $result->fresh([
                    'labOrderItem.labOrder.patient',
                    'labOrderItem.labTest',
                    'resultedBy',
                    'validatedBy',
                ]);
            }
        );
    }
}