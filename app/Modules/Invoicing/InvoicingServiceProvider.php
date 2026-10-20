<?php

namespace App\Modules\Invoicing;

use App\Modules\Invoicing\Models\Invoice;
use App\Modules\ModuleServiceProvider;
use App\Modules\Orders\Models\Order;

class InvoicingServiceProvider extends ModuleServiceProvider
{
    /**
     * Bootstrap the module.
     */
    public function boot(): void
    {
        parent::boot();

        Order::resolveRelationUsing('invoice', fn (Order $order) => $order->hasOne(Invoice::class));
    }
}
