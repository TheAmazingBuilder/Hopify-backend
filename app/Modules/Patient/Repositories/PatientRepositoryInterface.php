<?php

namespace App\Modules\Patient\Repositories;

use App\Modules\Patient\Models\Patient;
use Illuminate\Pagination\LengthAwarePaginator;

interface PatientRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findByUuid(string $uuid): ?Patient;
    public function create(array $data): Patient;
    public function update(string $uuid, array $data): bool;
    public function delete(string $uuid): bool;
    
    public function findByMrn(string $mrn): ?Patient;
    public function searchByName(string $query, int $perPage): LengthAwarePaginator;
    public function findWithActiveHospitalization(string $id): ?Patient;
}
