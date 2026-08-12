<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Modules\Hospitalization\Models\Bed;

class BedFactory extends Factory
{
    protected $model = Bed::class;

    public function definition(): array
    {
        return [
            'room_uuid' => \App\Modules\Hospitalization\Models\Room::factory(),
            'name' => 'B' . $this->faker->randomNumber(2),
            'type' => $this->faker->randomElement(['standard', 'icu', 'pediatric', 'bariatric']),
            'status' => 'available',
            'status_changed_at' => now(),
        ];
    }

    public function occupied(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'occupied',
            'status_changed_at' => now(),
        ]);
    }
}
