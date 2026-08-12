<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Modules\Hospitalization\Models\Hospitalization;

class HospitalizationFactory extends Factory
{
    protected $model = Hospitalization::class;

    public function definition(): array
    {
        return [
            'patient_uuid' => \App\Modules\Patient\Models\Patient::factory(),
            'bed_uuid' => \App\Modules\Hospitalization\Models\Bed::factory()->occupied(),
            'admitted_by_uuid' => \App\Modules\Hr\Models\Employee::factory(),
            'attending_doctor_uuid' => \App\Modules\Hr\Models\Employee::factory(),
            'admission_diagnosis' => $this->faker->sentence(10),
            'discharge_diagnosis' => null,
            'admitted_at' => now(),
            'discharged_at' => null,
            'status' => 'active',
            'discharge_notes' => null,
            'discharge_type' => null,
        ];
    }

    public function discharged(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'discharged',
            'discharged_at' => now(),
            'discharge_type' => $this->faker->randomElement(['planned', 'ama', 'transfer', 'deceased']),
        ]);
    }
}
