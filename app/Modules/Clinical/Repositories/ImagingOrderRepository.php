<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Repositories;

use App\Modules\Clinical\Models\ImagingOrder;
use App\Shared\Enums\ImagingOrderStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ImagingOrderRepository implements ImagingOrderRepositoryInterface
{
    protected array $defaultRelations = [
        'patient',
        'orderedBy',
        'consultation',
        'result.radiologist',
    ];

    public function getAll(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {
        return ImagingOrder::query()
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
                isset($filters['urgency']),
                fn ($query) =>
                    $query->where(
                        'urgency',
                        $filters['urgency']
                    )
            )

            ->when(
                isset($filters['modality']),
                fn ($query) =>
                    $query->where(
                        'modality',
                        $filters['modality']
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
    ): ?ImagingOrder {
        $query = ImagingOrder::query();

        if ($withRelations) {
            $query->with($this->defaultRelations);
        }

        return $query->find($uuid);
    }

    public function findForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator {
        return ImagingOrder::query()
            ->with([
                'orderedBy',
                'consultation',
                'result.radiologist',
            ])
            ->where('patient_uuid', $patientUuid)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function findForRadiologist(
        string $radiologistUuid,
        int $perPage = 15
    ): LengthAwarePaginator {
        return ImagingOrder::query()
            ->with([
                'patient',
                'consultation',
                'result.radiologist',
            ])
            ->whereHas(
                'result',
                fn ($query) =>
                    $query->where(
                        'radiologist_uuid',
                        $radiologistUuid
                    )
            )
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function getPendingForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator {
        return ImagingOrder::query()
            ->with([
                'orderedBy',
                'consultation',
            ])
            ->where('patient_uuid', $patientUuid)
            ->whereIn('status', [
                ImagingOrderStatus::Pending->value,
                ImagingOrderStatus::InProgress->value,
            ])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function getCompletedForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator {
        return ImagingOrder::query()
            ->with([
                'orderedBy',
                'result.radiologist',
            ])
            ->where('patient_uuid', $patientUuid)
            ->where(
                'status',
                ImagingOrderStatus::Completed->value
            )
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function getCriticalForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator {
        return ImagingOrder::query()
            ->with([
                'orderedBy',
                'result.radiologist',
            ])
            ->where('patient_uuid', $patientUuid)
            ->whereHas(
                'result',
                fn ($query) =>
                    $query->where('is_critical', true)
            )
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function create(array $data): ImagingOrder
    {
        return ImagingOrder::create($data);
    }
}