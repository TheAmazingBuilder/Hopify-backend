<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Modules\Hospitalization\Models\BedAssignment;

class BedAssignmentFactory extends Factory
{
    protected $model = BedAssignment::class;

    public function definition(): array
    {
        return [
            'hospitalization_uuid' => \App\Modules\Hospitalization\Models\Hospitalization::factory(),
            'bed_uuid' => \App\Modules\Hospitalization\Models\Bed::factory(),
            'assigned_at' => now(),
            'released_at' => null,
            'assigned_by_uuid' => \App\Modules\Hr\Models\Employee::factory(),
        ];
    }
}
