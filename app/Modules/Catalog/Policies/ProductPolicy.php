<?php

namespace App\Modules\Catalog\Policies;

use App\Modules\Catalog\Models\Product;
use App\Modules\Identity\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Farmers manage only their own products (admins use the admin product routes).
 */
class ProductPolicy
{
    public function create(User $user): Response
    {
        return $user->isAdmin() || $user->isVerified()
            ? Response::allow()
            : Response::deny('Debes ser un agricultor verificado para publicar productos.');
    }

    public function view(User $user, Product $product): Response
    {
        return $this->owns($user, $product);
    }

    public function update(User $user, Product $product): Response
    {
        return $this->owns($user, $product);
    }

    public function delete(User $user, Product $product): Response
    {
        return $this->owns($user, $product);
    }

    private function owns(User $user, Product $product): Response
    {
        return $product->user_id === $user->id
            ? Response::allow()
            : Response::deny('No tienes permiso para gestionar este producto.');
    }
}
