<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Enums\Auth\UserRole;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //admin user
        User::factory()->create([
            'name' => 'admin user',
            'email' => 'admin@example.com',
            'password' => 'sa123456',
            'phone' => '+201098542132',
            'role' => UserRole::ADMIN->value,
        ]);

        //customer user
        User::factory()->create([
            'name' => 'customer user',
            'email' => 'customer@example.com',
            'password' => 'sa123456',
            'phone' => '+201098532632',
            'role' => UserRole::CUSTOMER->value,
        ]);

        //Restaurant Manager user
        User::factory()->create([
            'name' => 'restaurant manager user',
            'email' => 'restaurant_manager@example.com',
            'password' => 'sa123456',
            'phone' => '+201012345678',
            'role' => UserRole::Restaurant_Manager->value,
        ]);
    }
}