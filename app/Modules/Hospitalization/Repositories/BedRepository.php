<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Repositories;

use App\Modules\Hospitalization\Models\Bed;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BedRepository implements BedRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Bed::query()
            ->when(isset($filters['search']), fn($q) => $q->where('name', 'like', "%{$filters['search']}%"))
            ->when(isset($filters['room_uuid']), fn($q) => $q->where('room_uuid', $filters['room_uuid']))
            ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->with('room.department')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findByUuid(string $uuid): ?Bed
    {
        return Bed::with(['room.department', 'currentHospitalization.patient'])->find($uuid);
    }

    public function create(array $data): Bed
    {
        return Bed::create($data);
    }

    public function update(string $uuid, array $data): bool
    {
        $bed = Bed::find($uuid);
        return $bed ? $bed->update($data) : false;
    }

    public function delete(string $uuid): bool
    {
        $bed = Bed::find($uuid);
        return $bed ? $bed->delete() : false;
    }

    public function getAvailableByRoom(string $roomUuid): Collection
    {
        return Bed::available()->where('room_uuid', $roomUuid)->get();
    }

    public function updateStatus(string $uuid, string $status): bool
    {
        $bed = Bed::find($uuid);
        if (! $bed) {
            return false;
        }
        return $bed->update([
            'status' => $status,
            'status_changed_at' => now(),
        ]);
    }
}
