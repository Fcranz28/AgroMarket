<?php

namespace App\Modules\Identity\Policies;

use App\Modules\Identity\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Admin actions on other accounts.
 */
class UserPolicy
{
    public function ban(User $admin, User $user): Response
    {
        return $admin->isAdmin() && ! $user->isAdmin() && ! $user->is($admin)
            ? Response::allow()
            : Response::deny('No puedes suspender a un administrador.');
    }

    public function verify(User $admin, User $user): Response
    {
        return $admin->isAdmin() && $user->isFarmer()
            ? Response::allow()
            : Response::deny('Solo se pueden verificar cuentas de agricultor.');
    }
}
