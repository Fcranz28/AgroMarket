<?php

namespace App\Modules\Orders;

use App\Modules\Catalog\Contracts\PurchaseHistory;
use App\Modules\ModuleServiceProvider;
use App\Modules\Orders\Events\OrderPlaced;
use App\Modules\Orders\Listeners\SendOrderPlacedNotifications;
use App\Modules\Orders\Livewire\CartCount;
use App\Modules\Orders\Livewire\CartSidebar;
use App\Modules\Orders\Services\OrderPurchaseHistory;
use Livewire\Component as LivewireComponent;

class OrdersServiceProvider extends ModuleServiceProvider
{
    /**
     * @var array<class-string, list<class-string>>
     */
    protected array $listen = [
        OrderPlaced::class => [SendOrderPlacedNotifications::class],
    ];

    /**
     * @var array<string, class-string<LivewireComponent>>
     */
    protected array $livewire = [
        'orders.cart-sidebar' => CartSidebar::class,
        'orders.cart-count' => CartCount::class,
    ];

    /**
     * Register the module services.
     */
    public function register(): void
    {
        $this->app->bind(PurchaseHistory::class, OrderPurchaseHistory::class);
    }
}
