<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Modules\Hospitalization\Models\Room;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'department_uuid' => \App\Modules\Foundation\Models\Department::factory(),
            'name' => $this->faker->word() . ' ' . $this->faker->randomNumber(2),
            'type' => $this->faker->randomElement(['consultation', 'recovery', 'surgery']),
            'floor' => $this->faker->numberBetween(0, 10),
            'capacity' => $this->faker->numberBetween(1, 6),
            'is_active' => true,
        ];
    }
}
