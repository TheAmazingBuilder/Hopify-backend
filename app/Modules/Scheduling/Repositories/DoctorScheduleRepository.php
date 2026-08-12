<?php

declare(strict_types=1);

namespace App\Modules\Scheduling\Repositories;

use App\Modules\Scheduling\Models\DoctorSchedule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class DoctorScheduleRepository implements DoctorScheduleRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return DoctorSchedule::query()
            ->when(isset($filters['doctor_uuid']), fn($q) => $q->byDoctor($filters['doctor_uuid']))
            ->when(isset($filters['day_of_week']), fn($q) => $q->byDay((int) $filters['day_of_week']))
            ->when(isset($filters['is_active']), fn($q) => $q->where('is_active', $filters['is_active']))
            ->with('doctor')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->paginate($perPage);
    }

    public function findByUuid(string $uuid): ?DoctorSchedule
    {
        return DoctorSchedule::with('doctor')->find($uuid);
    }

    public function create(array $data): DoctorSchedule
    {
        return DoctorSchedule::create($data);
    }

    public function update(string $uuid, array $data): bool
    {
        $schedule = DoctorSchedule::find($uuid);
        return $schedule ? $schedule->update($data) : false;
    }

    public function delete(string $uuid): bool
    {
        $schedule = DoctorSchedule::find($uuid);
        return $schedule ? $schedule->delete() : false;
    }

    public function getByDoctor(string $doctorUuid): Collection
    {
        return DoctorSchedule::byDoctor($doctorUuid)->active()->get();
    }

    public function getByDoctorAndDay(string $doctorUuid, int $dayOfWeek): Collection
    {
        return DoctorSchedule::byDoctor($doctorUuid)->byDay($dayOfWeek)->active()->get();
    }
}
