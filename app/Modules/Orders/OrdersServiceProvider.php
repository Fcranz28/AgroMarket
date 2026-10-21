<?php

namespace App\Modules\Orders;

use App\Modules\Catalog\Contracts\PurchaseHistory;
use App\Modules\ModuleServiceProvider;
use App\Modules\Orders\Services\OrderPurchaseHistory;

class OrdersServiceProvider extends ModuleServiceProvider
{
    /**
     * Register the module services.
     */
    public function register(): void
    {
        $this->app->bind(PurchaseHistory::class, OrderPurchaseHistory::class);
    }
}
