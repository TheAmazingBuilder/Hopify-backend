<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Repositories;

use App\Modules\Clinical\Models\Prescription;
use App\Shared\Enums\PrescriptionStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Modules\Clinical\Models\PrescriptionItem;

class PrescriptionRepository implements PrescriptionRepositoryInterface
{
    protected array $defaultRelations = [
        'patient',
        'doctor',
        'consultation',
        'dispensedBy',
        'items.medication.category',
    ];

    public function getAll(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {
        return Prescription::query()
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
                isset($filters['doctor_uuid']),
                fn ($query) =>
                    $query->where(
                        'doctor_uuid',
                        $filters['doctor_uuid']
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
    ): ?Prescription {
        $query = Prescription::query();

        if ($withRelations) {
            $query->with($this->defaultRelations);
        }

        return $query->find($uuid);
    }

    public function findForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator {
        return Prescription::query()
            ->with([
                'doctor',
                'consultation',
                'items.medication.category',
                'dispensedBy',
            ])
            ->where('patient_uuid', $patientUuid)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function findForDoctor(
        string $doctorUuid,
        int $perPage = 15
    ): LengthAwarePaginator {
        return Prescription::query()
            ->with([
                'patient',
                'consultation',
                'items.medication.category',
                'dispensedBy',
            ])
            ->where('doctor_uuid', $doctorUuid)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function getActiveForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator {
        return Prescription::query()
            ->with([
                'doctor',
                'consultation',
                'items.medication.category',
            ])
            ->where('patient_uuid', $patientUuid)
            ->where(
                'status',
                PrescriptionStatus::Active->value
            )
            ->where(function ($query): void {
                $query
                    ->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            })
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function getDispensedForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator {
        return Prescription::query()
            ->with([
                'doctor',
                'items.medication.category',
                'dispensedBy',
            ])
            ->where('patient_uuid', $patientUuid)
            ->where(
                'status',
                PrescriptionStatus::Dispensed->value
            )
            ->orderByDesc('dispensed_at')
            ->paginate($perPage);
    }

    public function create(array $data): Prescription
    {
        return Prescription::create($data);
    }

    public function createItem(array $data): PrescriptionItem
    {
        return PrescriptionItem::create($data);
    }

    public function update(
        string $uuid,
        array $data
    ): bool {
        $prescription = Prescription::query()->find($uuid);

        if (! $prescription) {
            return false;
        }

        return $prescription->update($data);
    }
}