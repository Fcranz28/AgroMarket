<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrNew(['email' => 'admin@agromarket.com']);

        if ($admin->exists) {
            $this->command?->info('El usuario administrador ya existe; no se modificó.');

            return;
        }

        $password = Str::password(16);

        $admin->forceFill([
            'name' => 'Administrador',
            'password' => $password,
            'role' => User::ROLE_ADMIN,
            'onboarding_completed' => true,
            'email_verified_at' => now(),
        ])->save();

        $this->command?->info('Usuario administrador listo: admin@agromarket.com');
        $this->command?->warn("Contraseña generada (cámbiala al iniciar sesión): {$password}");
    }
}
