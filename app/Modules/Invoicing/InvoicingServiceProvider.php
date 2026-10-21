<?php

namespace App\Modules\Invoicing;

use App\Modules\Invoicing\Listeners\GenerateInvoiceForOrder;
use App\Modules\Invoicing\Models\Invoice;
use App\Modules\ModuleServiceProvider;
use App\Modules\Orders\Events\OrderPlaced;
use App\Modules\Orders\Models\Order;

class InvoicingServiceProvider extends ModuleServiceProvider
{
    /**
     * @var array<class-string, list<class-string>>
     */
    protected array $listen = [
        OrderPlaced::class => [GenerateInvoiceForOrder::class],
    ];

    /**
     * Bootstrap the module.
     */
    public function boot(): void
    {
        parent::boot();

        Order::resolveRelationUsing('invoice', fn (Order $order) => $order->hasOne(Invoice::class));
    }
}
