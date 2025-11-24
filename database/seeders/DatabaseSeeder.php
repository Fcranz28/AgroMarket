<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@agromarket.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('password'),
                'role' => User::ROLE_ADMIN,
                'verification_status' => User::STATUS_APPROVED,
            ]
        );

        // Create Test User
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'role' => User::ROLE_USER,
            ]
        );

        $this->call(CategorySeeder::class);
    }
}
