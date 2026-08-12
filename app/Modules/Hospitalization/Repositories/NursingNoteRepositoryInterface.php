<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Repositories;

use App\Modules\Hospitalization\Models\NursingNote;
use Illuminate\Pagination\LengthAwarePaginator;

interface NursingNoteRepositoryInterface
{
    public function getByHospitalization(string $hospitalizationUuid, int $perPage = 15): LengthAwarePaginator;
    public function findByUuid(string $uuid): ?NursingNote;
    public function create(array $data): NursingNote;
    public function update(string $uuid, array $data): bool;
    public function delete(string $uuid): bool;
}
