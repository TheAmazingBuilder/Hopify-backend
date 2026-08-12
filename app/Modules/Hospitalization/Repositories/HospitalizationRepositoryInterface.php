<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Repositories;

use App\Modules\Hospitalization\Models\Hospitalization;
use Illuminate\Pagination\LengthAwarePaginator;

interface HospitalizationRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findByUuid(string $uuid): ?Hospitalization;
    public function create(array $data): Hospitalization;
    public function update(string $uuid, array $data): bool;
    public function getActiveByPatient(string $patientUuid): ?Hospitalization;
    public function getActiveByBed(string $bedUuid): ?Hospitalization;
    public function getActiveCount(): int;
    public function delete(string $uuid): bool;
}
