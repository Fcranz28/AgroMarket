<?php

namespace App\Modules\Orders\Services;

use App\Modules\Catalog\Contracts\PurchaseHistory;
use App\Modules\Catalog\Models\Product;
use App\Modules\Identity\Models\User;
use App\Modules\Orders\Models\OrderItem;

class OrderPurchaseHistory implements PurchaseHistory
{
    public function hasPaidPurchase(User $user, Product $product): bool
    {
        return OrderItem::where('product_id', $product->id)
            ->whereHas('order', fn ($query) => $query->where('user_id', $user->id)->where('payment_status', 'paid'))
            ->exists();
    }
}
