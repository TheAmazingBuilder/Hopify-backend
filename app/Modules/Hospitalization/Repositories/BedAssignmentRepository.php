<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Repositories;

use App\Modules\Hospitalization\Models\BedAssignment;
use Illuminate\Database\Eloquent\Collection;

class BedAssignmentRepository implements BedAssignmentRepositoryInterface
{
    public function getByHospitalization(string $hospitalizationUuid): Collection
    {
        return BedAssignment::where('hospitalization_uuid', $hospitalizationUuid)
            ->with('bed', 'assignedBy')
            ->orderByDesc('assigned_at')
            ->get();
    }

    public function create(array $data): BedAssignment
    {
        return BedAssignment::create($data);
    }

    public function releaseCurrent(string $hospitalizationUuid): bool
    {
        $assignment = BedAssignment::where('hospitalization_uuid', $hospitalizationUuid)
            ->whereNull('released_at')
            ->first();

        return $assignment ? $assignment->update(['released_at' => now()]) : false;
    }
}
