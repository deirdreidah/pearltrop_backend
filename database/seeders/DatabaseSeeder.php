<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\CarBooking;
use App\Models\Accommodation;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin user
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        // Dummy data
        Car::factory(10)->create();
        Accommodation::factory(5)->create();
        
        // Random users for bookings and reviews
        User::factory(20)->create();

        CarBooking::factory(50)->create();
        Review::factory(30)->create();
    }
}
