<?php

namespace App\Modules\Payments\Jobs;

use App\Modules\Orders\Enums\PaymentStatus;
use App\Modules\Orders\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

/**
 * Applies a verified Stripe webhook event in the background ("payments" queue), so the
 * webhook answers Stripe right away.
 */
class HandleStripeEvent implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    /**
     * Seconds to wait before each retry.
     *
     * @var list<int>
     */
    public array $backoff = [10, 60, 300, 900];

    public function __construct(public string $type, public string $paymentIntentId)
    {
        $this->onQueue('payments');
    }

    public function handle(): void
    {
        match ($this->type) {
            'payment_intent.succeeded' => Order::where('stripe_payment_intent_id', $this->paymentIntentId)
                ->where('payment_status', PaymentStatus::Pending)
                ->update(['payment_status' => PaymentStatus::Paid]),
            'payment_intent.payment_failed' => Log::warning('Stripe payment failed', ['payment_intent' => $this->paymentIntentId]),
            default => null,
        };
    }
}
