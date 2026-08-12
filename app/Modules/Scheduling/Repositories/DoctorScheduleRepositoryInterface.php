<?php

declare(strict_types=1);

namespace App\Modules\Scheduling\Repositories;

use App\Modules\Scheduling\Models\DoctorSchedule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface DoctorScheduleRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findByUuid(string $uuid): ?DoctorSchedule;
    public function create(array $data): DoctorSchedule;
    public function update(string $uuid, array $data): bool;
    public function delete(string $uuid): bool;
    public function getByDoctor(string $doctorUuid): Collection;
    public function getByDoctorAndDay(string $doctorUuid, int $dayOfWeek): Collection;
}
