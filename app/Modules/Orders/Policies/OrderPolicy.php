<?php

namespace App\Modules\Orders\Policies;

use App\Modules\Identity\Models\User;
use App\Modules\Orders\Models\Order;

class OrderPolicy
{
    /**
     * The buyer sees their own order.
     */
    public function view(User $user, Order $order): bool
    {
        return $order->user_id !== null && $order->user_id === $user->id;
    }

    /**
     * A farmer sees the orders that include at least one of their products.
     */
    public function viewAsSeller(User $user, Order $order): bool
    {
        return $user->isFarmer() && $order->hasItemsSoldBy($user);
    }

    /**
     * Only a farmer that sells every item of the order may change its status.
     */
    public function updateStatus(User $user, Order $order): bool
    {
        return $user->isFarmer() && $order->isFullySoldBy($user);
    }
}
