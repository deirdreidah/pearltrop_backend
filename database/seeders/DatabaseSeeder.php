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
        // 1. Roles & Permissions setup
        $adminRole = \App\Models\Role::firstOrCreate(['name' => 'Super Admin']);
        \App\Models\Role::firstOrCreate(['name' => 'Admin']);
        \App\Models\Role::firstOrCreate(['name' => 'User']);

        $this->call(DefaultPermissionsSeeder::class);
        
        // Assign all permissions to Super Admin role
        $allPermissions = \App\Models\Permission::all();
        $adminRole->permissions()->sync($allPermissions->pluck('id'));

        // 2. Admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
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
