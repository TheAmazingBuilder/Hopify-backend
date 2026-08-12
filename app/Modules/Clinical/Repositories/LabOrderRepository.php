<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Repositories;

use App\Modules\Clinical\Models\LabOrder;
use App\Shared\Enums\LabOrderPriority;
use App\Shared\Enums\LabOrderStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Modules\Clinical\Models\LabOrderItem;
use App\Modules\Clinical\Models\LabResult;

class LabOrderRepository implements LabOrderRepositoryInterface
{
    protected array $defaultRelations = [
        'patient',
        'orderedBy',
        'collectedBy',
        'consultation',
        'items.labTest',
        'items.result.resultedBy',
        'items.result.validatedBy',
    ];

    public function getAll(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {
        return LabOrder::query()
            ->with($this->defaultRelations)

            ->when(
                isset($filters['patient_uuid']),
                fn ($query) =>
                    $query->where(
                        'patient_uuid',
                        $filters['patient_uuid']
                    )
            )

            ->when(
                isset($filters['ordered_by_uuid']),
                fn ($query) =>
                    $query->where(
                        'ordered_by_uuid',
                        $filters['ordered_by_uuid']
                    )
            )

            ->when(
                isset($filters['consultation_uuid']),
                fn ($query) =>
                    $query->where(
                        'consultation_uuid',
                        $filters['consultation_uuid']
                    )
            )

            ->when(
                isset($filters['status']),
                fn ($query) =>
                    $query->where(
                        'status',
                        $filters['status']
                    )
            )

            ->when(
                isset($filters['priority']),
                fn ($query) =>
                    $query->where(
                        'priority',
                        $filters['priority']
                    )
            )

            ->when(
                isset($filters['order_number']),
                fn ($query) =>
                    $query->where(
                        'order_number',
                        $filters['order_number']
                    )
            )

            ->when(
                isset($filters['date_from']),
                fn ($query) =>
                    $query->whereDate(
                        'created_at',
                        '>=',
                        $filters['date_from']
                    )
            )

            ->when(
                isset($filters['date_to']),
                fn ($query) =>
                    $query->whereDate(
                        'created_at',
                        '<=',
                        $filters['date_to']
                    )
            )

            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function findByUuid(
        string $uuid,
        bool $withRelations = true
    ): ?LabOrder {
        $query = LabOrder::query();

        if ($withRelations) {
            $query->with($this->defaultRelations);
        }

        return $query->find($uuid);
    }

    public function findForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator {
        return LabOrder::query()
            ->with([
                'orderedBy',
                'consultation',
                'items.labTest',
                'items.result',
            ])
            ->where('patient_uuid', $patientUuid)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function findForDoctor(
        string $doctorUuid,
        int $perPage = 15
    ): LengthAwarePaginator {
        return LabOrder::query()
            ->with([
                'patient',
                'consultation',
                'items.labTest',
                'items.result',
            ])
            ->where('ordered_by_uuid', $doctorUuid)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function getPendingForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator {
        return LabOrder::query()
            ->with([
                'orderedBy',
                'items.labTest',
            ])
            ->where('patient_uuid', $patientUuid)
            ->whereIn('status', [
                LabOrderStatus::Pending->value,
                LabOrderStatus::InProgress->value,
            ])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function getCompletedForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator {
        return LabOrder::query()
            ->with([
                'orderedBy',
                'items.labTest',
                'items.result.resultedBy',
                'items.result.validatedBy',
            ])
            ->where('patient_uuid', $patientUuid)
            ->where(
                'status',
                LabOrderStatus::Completed->value
            )
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function getWithAbnormalResults(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator {
        return LabOrder::query()
            ->with([
                'orderedBy',
                'items.labTest',
                'items.result',
            ])
            ->where('patient_uuid', $patientUuid)
            ->whereHas(
                'items.result',
                fn ($query) =>
                    $query->where('is_abnormal', true)
            )
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function create(array $data): LabOrder
    {
        return LabOrder::create($data);
    }

    public function createItem(array $data): LabOrderItem
    {
        return LabOrderItem::create($data);
    }

    public function update(
        string $uuid,
        array $data
    ): bool {
        $order = LabOrder::query()->find($uuid);

        if (! $order) {
            return false;
        }

        return $order->update($data);
    }

    public function updateItem(
        string $uuid,
        array $data
    ): bool {
        $item = LabOrderItem::query()->find($uuid);

        if (! $item) {
            return false;
        }

        return $item->update($data);
    }

    public function findItemByUuid(
        string $uuid
    ): ?LabOrderItem {
        return LabOrderItem::query()
            ->with([
                'labOrder',
                'labTest',
                'result',
            ])
            ->find($uuid);
    }

    public function createResult(array $data): LabResult
    {
        return LabResult::create($data);
    }

    public function updateResult(
        string $uuid,
        array $data
    ): bool {
        $result = LabResult::query()->find($uuid);

        if (! $result) {
            return false;
        }

        return $result->update($data);
    }
}