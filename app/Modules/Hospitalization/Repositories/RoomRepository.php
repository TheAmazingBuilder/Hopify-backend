<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Repositories;

use App\Modules\Hospitalization\Models\Room;
use Illuminate\Pagination\LengthAwarePaginator;

class RoomRepository implements RoomRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Room::query()
            ->when(isset($filters['search']), fn($q) => $q->where(function ($sub) use ($filters) {
                $sub->where('name', 'like', "%{$filters['search']}%");
            }))
            ->when(isset($filters['department_uuid']), fn($q) => $q->where('department_uuid', $filters['department_uuid']))
            ->when(isset($filters['floor']), fn($q) => $q->where('floor', $filters['floor']))
            ->when(isset($filters['is_active']), fn($q) => $q->where('is_active', $filters['is_active']))
            ->withCount('beds')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findByUuid(string $uuid): ?Room
    {
        return Room::with(['department', 'beds'])->find($uuid);
    }

    public function create(array $data): Room
    {
        return Room::create($data);
    }

    public function update(string $uuid, array $data): bool
    {
        $room = Room::find($uuid);
        return $room ? $room->update($data) : false;
    }

    public function delete(string $uuid): bool
    {
        $room = Room::find($uuid);
        return $room ? $room->delete() : false;
    }
}
