<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PlaceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->city() . ' Safari Park',
            'location' => $this->faker->city(),
            'description' => $this->faker->paragraph(),
            'category' => $this->faker->randomElement(['Nature', 'Cultural', 'Urban', 'Adventure']),
        ];
    }
}
