<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Repositories;

use App\Modules\Hospitalization\Models\Hospitalization;
use Illuminate\Pagination\LengthAwarePaginator;

class HospitalizationRepository implements HospitalizationRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Hospitalization::query()
            ->when(isset($filters['search']), fn($q) => $q->where(function ($sub) use ($filters) {
                $sub->where('admission_diagnosis', 'like', "%{$filters['search']}%")
                    ->orWhereHas('patient', fn($pat) => $pat->search($filters['search']));
            }))
            ->when(isset($filters['patient_uuid']), fn($q) => $q->where('patient_uuid', $filters['patient_uuid']))
            ->when(isset($filters['bed_uuid']), fn($q) => $q->where('bed_uuid', $filters['bed_uuid']))
            ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['active_only']), fn($q) => $q->active())
            ->with(['patient', 'bed.room.department', 'admittedBy', 'attendingDoctor'])
            ->orderByDesc('admitted_at')
            ->paginate($perPage);
    }

    public function findByUuid(string $uuid): ?Hospitalization
    {
        return Hospitalization::with([
            'patient', 'bed.room.department',
            'admittedBy', 'attendingDoctor',
            'nursingNotes.nurse', 'doctorRounds.doctor',
            'bedAssignments.bed', 'bedAssignments.assignedBy',
        ])->find($uuid);
    }

    public function create(array $data): Hospitalization
    {
        return Hospitalization::create($data);
    }

    public function update(string $uuid, array $data): bool
    {
        $hosp = Hospitalization::find($uuid);
        return $hosp ? $hosp->update($data) : false;
    }

    public function getActiveByPatient(string $patientUuid): ?Hospitalization
    {
        return Hospitalization::active()->where('patient_uuid', $patientUuid)->first();
    }

    public function getActiveByBed(string $bedUuid): ?Hospitalization
    {
        return Hospitalization::active()->where('bed_uuid', $bedUuid)->first();
    }

    public function getActiveCount(): int
    {
        return Hospitalization::active()->count();
    }

    public function delete(string $uuid): bool
    {
        $hosp = Hospitalization::find($uuid);
        return $hosp ? $hosp->delete() : false;
    }
}
