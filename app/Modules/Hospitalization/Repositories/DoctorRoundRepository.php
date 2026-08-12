<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Repositories;

use App\Modules\Hospitalization\Models\DoctorRound;
use Illuminate\Pagination\LengthAwarePaginator;

class DoctorRoundRepository implements DoctorRoundRepositoryInterface
{
    public function getByHospitalization(string $hospitalizationUuid, int $perPage = 15): LengthAwarePaginator
    {
        return DoctorRound::where('hospitalization_uuid', $hospitalizationUuid)
            ->with('doctor')
            ->orderByDesc('occurred_at')
            ->paginate($perPage);
    }

    public function findByUuid(string $uuid): ?DoctorRound
    {
        return DoctorRound::with('doctor')->find($uuid);
    }

    public function create(array $data): DoctorRound
    {
        return DoctorRound::create($data);
    }

    public function update(string $uuid, array $data): bool
    {
        $round = DoctorRound::find($uuid);
        return $round ? $round->update($data) : false;
    }

    public function delete(string $uuid): bool
    {
        $round = DoctorRound::find($uuid);
        return $round ? $round->delete() : false;
    }
}
