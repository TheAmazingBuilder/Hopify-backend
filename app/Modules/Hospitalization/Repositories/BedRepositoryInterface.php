<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Repositories;

use App\Modules\Hospitalization\Models\Bed;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BedRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findByUuid(string $uuid): ?Bed;
    public function create(array $data): Bed;
    public function update(string $uuid, array $data): bool;
    public function delete(string $uuid): bool;
    public function getAvailableByRoom(string $roomUuid): Collection;
    public function updateStatus(string $uuid, string $status): bool;
}
