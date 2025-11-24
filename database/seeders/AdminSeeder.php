<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@agromarket.com',
            'password' => Hash::make('admin123'),
            'role' => User::ROLE_ADMIN,
            'onboarding_completed' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Usuario administrador creado exitosamente!');
        $this->command->info('Email: admin@agromarket.com');
        $this->command->info('Contraseña: admin123');
    }
}
