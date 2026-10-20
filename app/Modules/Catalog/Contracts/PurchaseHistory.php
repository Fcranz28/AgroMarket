<?php

namespace App\Modules\Catalog\Contracts;

use App\Modules\Catalog\Models\Product;
use App\Modules\Identity\Models\User;

/**
 * Purchase information the catalog needs (e.g. only buyers may review a product).
 * Implemented by the Orders module, so Catalog does not depend on it.
 */
interface PurchaseHistory
{
    public function hasPaidPurchase(User $user, Product $product): bool;
}
