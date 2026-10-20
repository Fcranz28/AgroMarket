<?php

namespace App\Modules\Payments\Listeners;

use App\Modules\Orders\Events\OrderCancelled;
use App\Modules\Payments\Jobs\RefundPayment;

class RefundCancelledOrder
{
    /**
     * Queue the refund of a cancelled order that was paid (see RefundPayment).
     */
    public function handle(OrderCancelled $event): void
    {
        $order = $event->order;

        if ($order->payment_status === 'paid' && $order->stripe_payment_intent_id) {
            RefundPayment::dispatch($order->stripe_payment_intent_id, $order->id);
        }
    }
}
