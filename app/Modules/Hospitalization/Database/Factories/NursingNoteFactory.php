<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Modules\Hospitalization\Models\NursingNote;

class NursingNoteFactory extends Factory
{
    protected $model = NursingNote::class;

    public function definition(): array
    {
        return [
            'hospitalization_uuid' => \App\Modules\Hospitalization\Models\Hospitalization::factory(),
            'nurse_uuid' => \App\Modules\Hr\Models\Employee::factory(),
            'type' => $this->faker->randomElement(['general', 'medication', 'observation', 'wound']),
            'note' => $this->faker->paragraph(3),
            'noted_at' => now(),
        ];
    }
}
