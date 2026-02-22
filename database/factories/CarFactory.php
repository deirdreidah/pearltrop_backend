<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'brand' => $this->faker->randomElement(['Toyota', 'Nissan', 'Honda', 'Mercedes', 'BMW', 'Range Rover']),
            'model' => $this->faker->word(),
            'year' => $this->faker->numberBetween(2015, 2024),
            'description' => $this->faker->paragraph(),
            'price_per_day' => $this->faker->randomFloat(2, 50, 500),
            'is_available' => $this->faker->boolean(80),
        ];
    }
}
