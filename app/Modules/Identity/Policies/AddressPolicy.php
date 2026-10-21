<?php

namespace App\Modules\Identity\Policies;

use App\Modules\Identity\Models\Address;
use App\Modules\Identity\Models\User;

class AddressPolicy
{
    public function delete(User $user, Address $address): bool
    {
        return $address->user_id === $user->id;
    }
}
