<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Repositories;

use App\Modules\Hospitalization\Models\DoctorRound;
use Illuminate\Pagination\LengthAwarePaginator;

interface DoctorRoundRepositoryInterface
{
    public function getByHospitalization(string $hospitalizationUuid, int $perPage = 15): LengthAwarePaginator;
    public function findByUuid(string $uuid): ?DoctorRound;
    public function create(array $data): DoctorRound;
    public function update(string $uuid, array $data): bool;
    public function delete(string $uuid): bool;
}
