<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Modules\Hospitalization\Models\DoctorRound;

class DoctorRoundFactory extends Factory
{
    protected $model = DoctorRound::class;

    public function definition(): array
    {
        return [
            'hospitalization_uuid' => \App\Modules\Hospitalization\Models\Hospitalization::factory(),
            'doctor_uuid' => \App\Modules\Hr\Models\Employee::factory(),
            'subjective' => $this->faker->paragraph(2),
            'objective' => $this->faker->paragraph(2),
            'assessment' => $this->faker->paragraph(2),
            'plan' => $this->faker->paragraph(2),
            'occurred_at' => now(),
        ];
    }
}
