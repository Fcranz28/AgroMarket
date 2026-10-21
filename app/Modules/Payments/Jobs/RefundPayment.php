<?php

namespace App\Modules\Payments\Jobs;

use App\Modules\Orders\Models\Order;
use App\Modules\Payments\Services\PaymentGateway;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Refunds a Stripe payment in the background ("payments" queue), retrying while Stripe is
 * unavailable. Unique per payment intent so the same charge is never refunded twice.
 */
class RefundPayment implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    /**
     * Seconds to wait before each retry.
     *
     * @var list<int>
     */
    public array $backoff = [30, 120, 600, 1800];

    /**
     * @param  int|null  $orderId  order to mark as refunded; null when the payment never became an order
     */
    public function __construct(public string $paymentIntentId, public ?int $orderId = null)
    {
        $this->onQueue('payments');
    }

    public function uniqueId(): string
    {
        return $this->paymentIntentId;
    }

    public function handle(PaymentGateway $gateway): void
    {
        $gateway->refund($this->paymentIntentId);

        $this->order()?->update(['payment_status' => 'refunded']);
    }

    public function failed(Throwable $exception): void
    {
        $this->order()?->update(['payment_status' => 'refund_failed']);

        Log::critical('Automatic refund failed after retries', [
            'payment_intent' => $this->paymentIntentId,
            'order_id' => $this->orderId,
            'error' => $exception->getMessage(),
        ]);
    }

    private function order(): ?Order
    {
        return $this->orderId ? Order::find($this->orderId) : null;
    }
}
