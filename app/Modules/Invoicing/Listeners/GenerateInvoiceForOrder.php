<?php

namespace App\Modules\Invoicing\Listeners;

use App\Modules\Invoicing\Models\Invoice;
use App\Modules\Invoicing\Services\InvoiceService;
use App\Modules\Orders\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Issues the electronic receipt of a placed order in the background ("invoices" queue).
 */
class GenerateInvoiceForOrder implements ShouldQueue
{
    public string $queue = 'invoices';

    public int $tries = 3;

    /**
     * Seconds to wait before each retry.
     *
     * @var list<int>
     */
    public array $backoff = [10, 60, 300];

    public function __construct(private InvoiceService $invoices) {}

    public function handle(OrderPlaced $event): void
    {
        // A retry must never issue a second receipt for the same order.
        if (Invoice::where('order_id', $event->order->id)->exists()) {
            return;
        }

        $this->invoices->generateInvoice($event->order);
    }

    public function failed(OrderPlaced $event, Throwable $exception): void
    {
        Log::error('Invoice generation failed after retries', [
            'order_id' => $event->order->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
