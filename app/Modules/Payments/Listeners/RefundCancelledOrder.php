<?php

namespace App\Modules\Payments\Listeners;

use App\Modules\Orders\Events\OrderCancelled;
use App\Modules\Payments\Services\PaymentGateway;
use Illuminate\Support\Facades\Log;
use Throwable;

class RefundCancelledOrder
{
    public function __construct(private PaymentGateway $gateway) {}

    /**
     * Refund the Stripe charge of a cancelled order that was paid.
     */
    public function handle(OrderCancelled $event): void
    {
        $order = $event->order;

        if ($order->payment_status !== 'paid' || ! $order->stripe_payment_intent_id) {
            return;
        }

        try {
            $this->gateway->refund($order->stripe_payment_intent_id);
            $order->update(['payment_status' => 'refunded']);
        } catch (Throwable $exception) {
            Log::critical('Refund after cancellation failed', ['order_id' => $order->id, 'error' => $exception->getMessage()]);
        }
    }
}
