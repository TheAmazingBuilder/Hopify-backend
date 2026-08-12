<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Repositories;

use App\Modules\Hospitalization\Models\BedAssignment;
use Illuminate\Database\Eloquent\Collection;

interface BedAssignmentRepositoryInterface
{
    public function getByHospitalization(string $hospitalizationUuid): Collection;
    public function create(array $data): BedAssignment;
    public function releaseCurrent(string $hospitalizationUuid): bool;
}
