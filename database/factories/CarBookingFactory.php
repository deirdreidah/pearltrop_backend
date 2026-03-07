<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarBookingFactory extends Factory
{
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $endDate = (clone $startDate)->modify('+' . rand(1, 14) . ' days');

        return [
            'user_id' => User::factory(),
            'car_id' => Car::all()->random()?->id ?? Car::factory(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_price' => $this->faker->numberBetween(500000, 5000000),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled', 'completed']),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
