<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Repositories;

use App\Modules\Clinical\Models\Consultation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ConsultationRepository implements ConsultationRepositoryInterface
{
    /**
     * Relations couramment nécessaires pour l'écran
     * de détail d'une consultation.
     */
    protected array $defaultRelations = [
        'patient',
        'doctor',
        'appointment',
        'hospitalization',
        'vitalSigns',
        'diagnoses.icdCode',
        'prescriptions.items.medication',
        'labOrders.items.labTest',
        'labOrders.items.result',
        'imagingOrders.result',
    ];

    public function getAll(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {
        return Consultation::query()
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
                isset($filters['hospitalization_uuid']),
                fn ($query) =>
                    $query->where(
                        'hospitalization_uuid',
                        $filters['hospitalization_uuid']
                    )
            )

            ->when(
                isset($filters['appointment_uuid']),
                fn ($query) =>
                    $query->where(
                        'appointment_uuid',
                        $filters['appointment_uuid']
                    )
            )

            ->when(
                array_key_exists('is_finalized', $filters),
                fn ($query) =>
                    $query->where(
                        'is_finalized',
                        $filters['is_finalized']
                    )
            )

            ->when(
                isset($filters['date_from']),
                fn ($query) =>
                    $query->whereDate(
                        'consultation_date',
                        '>=',
                        $filters['date_from']
                    )
            )

            ->when(
                isset($filters['date_to']),
                fn ($query) =>
                    $query->whereDate(
                        'consultation_date',
                        '<=',
                        $filters['date_to']
                    )
            )

            ->orderByDesc('consultation_date')
            ->paginate($perPage);
    }

    public function findByUuid(
        string $uuid,
        bool $withRelations = true
    ): ?Consultation {
        $query = Consultation::query();

        if ($withRelations) {
            $query->with($this->defaultRelations);
        }

        return $query->find($uuid);
    }

    public function findForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator {
        return Consultation::query()
            ->with([
                'doctor',
                'appointment',
                'diagnoses.icdCode',
            ])
            ->where('patient_uuid', $patientUuid)
            ->orderByDesc('consultation_date')
            ->paginate($perPage);
    }

    public function findForDoctor(
        string $doctorUuid,
        int $perPage = 15
    ): LengthAwarePaginator {
        return Consultation::query()
            ->with([
                'patient',
                'appointment',
            ])
            ->where('doctor_uuid', $doctorUuid)
            ->orderByDesc('consultation_date')
            ->paginate($perPage);
    }

    public function getHistoryForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator {
        return Consultation::query()
            ->with([
                'doctor',
                'diagnoses.icdCode',
                'prescriptions.items.medication',
                'labOrders.items.labTest',
                'labOrders.items.result',
                'imagingOrders.result',
            ])
            ->where('patient_uuid', $patientUuid)
            ->orderByDesc('consultation_date')
            ->paginate($perPage);
    }

    public function getFinalizedForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator {
        return Consultation::query()
            ->with([
                'doctor',
                'diagnoses.icdCode',
            ])
            ->where('patient_uuid', $patientUuid)
            ->where('is_finalized', true)
            ->orderByDesc('consultation_date')
            ->paginate($perPage);
    }

    public function create(array $data): Consultation
    {
        return Consultation::create($data);
    }
}