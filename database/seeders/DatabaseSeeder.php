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
            'role' => UserRole::ADMIN->value,
        ]);

        //customer user
        User::factory()->create([
            'name' => 'customer user',
            'email' => 'customer@example.com',
            'password' => 'sa123456',
            'role' => UserRole::CUSTOMER->value,
        ]);

        //vendor user
        User::factory()->create([
            'name' => 'vendor user',
            'email' => 'vendor@example.com',
            'password' => 'sa123456',
            'role' => UserRole::VENDOR->value,
        ]);
    }
}