<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Actions;

use App\Modules\Clinical\DTOs\RecordLabResultDTO;
use App\Modules\Clinical\Models\LabResult;
use App\Modules\Clinical\Repositories\LabOrderRepositoryInterface;
use App\Modules\Hr\Models\Employee;
use App\Shared\Enums\LabAbnormalityLevel;
use App\Shared\Enums\LabOrderItemStatus;
use App\Shared\Enums\LabOrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class RecordLabResultAction
{
    public function __construct(
        private LabOrderRepositoryInterface $repository,
    ) {
    }

    public function execute(
        RecordLabResultDTO $dto,
        string $employeeUuid
    ): LabResult {
        $item = $this->repository->findItemByUuid(
            $dto->labOrderItemUuid
        );

        if (! $item) {
            throw ValidationException::withMessages([
                'lab_order_item_uuid' =>
                    'The specified laboratory order item does not exist.',
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

        if (
            $item->labOrder->status
            === LabOrderStatus::Cancelled
        ) {
            throw ValidationException::withMessages([
                'lab_order_item_uuid' =>
                    'A cancelled laboratory order cannot receive results.',
            ]);
        }

        if (
            $dto->isAbnormal
            && $dto->abnormalityLevel === null
        ) {
            throw ValidationException::withMessages([
                'abnormality_level' =>
                    'An abnormal result must have an abnormality level.',
            ]);
        }

        if (
            ! $dto->isAbnormal
            && $dto->abnormalityLevel !== null
        ) {
            throw ValidationException::withMessages([
                'abnormality_level' =>
                    'A normal result cannot have an abnormality level.',
            ]);
        }

        if (
            $dto->abnormalityLevel !== null
            && ! in_array(
                $dto->abnormalityLevel,
                array_column(
                    LabAbnormalityLevel::cases(),
                    'value'
                ),
                true
            )
        ) {
            throw ValidationException::withMessages([
                'abnormality_level' =>
                    'The specified abnormality level is invalid.',
            ]);
        }

        return DB::transaction(
            function () use (
                $dto,
                $employee,
                $item
            ): LabResult {
                $result = $this->repository->createResult([
                    'lab_order_item_uuid' =>
                        $item->uuid,
                    'value' => $dto->value,
                    'unit' => $dto->unit,
                    'reference_range' =>
                        $dto->referenceRange,
                    'is_abnormal' =>
                        $dto->isAbnormal,
                    'abnormality_level' =>
                        $dto->abnormalityLevel,
                    'notes' => $dto->notes,
                    'resulted_at' => now(),
                    'resulted_by_uuid' =>
                        $employee->uuid,
                ]);

                $this->repository->updateItem(
                    $item->uuid,
                    [
                        'status' =>
                            LabOrderItemStatus::Completed,
                    ]
                );

                $this->refreshOrderStatus(
                    $item->labOrder->uuid
                );

                return $result->load([
                    'labOrderItem.labTest',
                    'resultedBy',
                ]);
            }
        );
    }

    private function refreshOrderStatus(
        string $labOrderUuid
    ): void {
        $order = $this->repository->findByUuid(
            $labOrderUuid,
            true
        );

        if (! $order) {
            return;
        }

        $hasPendingItems = $order->items()
            ->where(
                'status',
                LabOrderItemStatus::Pending->value
            )
            ->exists();

        $newStatus = $hasPendingItems
            ? LabOrderStatus::InProgress
            : LabOrderStatus::Completed;

        $this->repository->update(
            $labOrderUuid,
            [
                'status' => $newStatus,
            ]
        );
    }
}