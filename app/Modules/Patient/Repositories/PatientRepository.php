<?php

namespace App\Modules\Patient\Repositories;

use App\Modules\Patient\Models\Patient;
use Illuminate\Pagination\LengthAwarePaginator;

class PatientRepository implements PatientRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Patient::query()
            ->when(isset($filters['search']), fn($q) => $q->search($filters['search']))
            ->when(isset($filters['gender']), fn($q) => $q->where('gender', $filters['gender']))
            ->when(isset($filters['is_deceased']), fn($q) => $q->where('is_deceased', $filters['is_deceased']))
            ->orderBy('lname')
            ->paginate($perPage);
    }

    public function findByUuid(string $uuid): ?Patient
    {
        return Patient::with(['contacts', 'insurances', 'allergies', 'antecedents'])->find($uuid);
    }

    public function create(array $data): Patient
    {
        return Patient::create($data);
    }

    public function update(string $uuid, array $data): bool
    {
        $patient = Patient::find($uuid);
        if (!$patient) return false;
        return $patient->update($data);
    }

    public function delete(string $uuid): bool
    {
        $patient = Patient::find($uuid);
        if (!$patient) return false;
        return $patient->delete();
    }

    public function findByMrn(string $mrn): ?Patient
    {
        return Patient::where('mrn', $mrn)->first();
    }

    public function searchByName(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return Patient::where('fname', 'like', "%{$query}%")
            ->orWhere('lname', 'like', "%{$query}%")
            ->orWhere('mrn', 'like', "%{$query}%")
            ->with(['allergies', 'insurances'])
            ->paginate($perPage);
    }

    public function findWithActiveHospitalization(string $uuid): ?Patient
    {
        return Patient::with(['contacts'])->find($uuid); 
    }
}
