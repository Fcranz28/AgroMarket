<?php

namespace App\Modules\Identity\Enums;

enum UserRole: string
{
    case User = 'user';
    case Farmer = 'farmer';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::User => 'Comprador',
            self::Farmer => 'Agricultor',
            self::Admin => 'Administrador',
        };
    }
}
