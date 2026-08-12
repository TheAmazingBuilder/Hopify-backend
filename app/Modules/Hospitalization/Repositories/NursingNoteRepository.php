<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Repositories;

use App\Modules\Hospitalization\Models\NursingNote;
use Illuminate\Pagination\LengthAwarePaginator;

class NursingNoteRepository implements NursingNoteRepositoryInterface
{
    public function getByHospitalization(string $hospitalizationUuid, int $perPage = 15): LengthAwarePaginator
    {
        return NursingNote::where('hospitalization_uuid', $hospitalizationUuid)
            ->with('nurse')
            ->orderByDesc('noted_at')
            ->paginate($perPage);
    }

    public function findByUuid(string $uuid): ?NursingNote
    {
        return NursingNote::with('nurse')->find($uuid);
    }

    public function create(array $data): NursingNote
    {
        return NursingNote::create($data);
    }

    public function update(string $uuid, array $data): bool
    {
        $note = NursingNote::find($uuid);
        return $note ? $note->update($data) : false;
    }

    public function delete(string $uuid): bool
    {
        $note = NursingNote::find($uuid);
        return $note ? $note->delete() : false;
    }
}
