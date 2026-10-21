<?php

namespace App\Modules\Orders\Actions;

use App\Modules\Catalog\Exceptions\InsufficientStockException;
use App\Modules\Catalog\Services\StockService;
use App\Modules\Orders\Models\Order;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PlaceOrderAction
{
    public function __construct(private StockService $stock) {}

    /**
     * Take the stock and create a paid order with its items in a single transaction.
     *
     * @param  Collection<int, array{product_id: int, product_unit_id: int|null, unit: string, quantity: int, price: string}>  $lines  priced by CartPricingService
     * @param  array{shipping_address: string, phone: string, guest_name: string, guest_lastname: string, guest_email: string, document_type: string, document_number: string}  $customer
     *
     * @throws InsufficientStockException when a line has no stock left
     * @throws UniqueConstraintViolationException when the payment intent was already used
     */
    public function execute(Collection $lines, int $totalCents, array $customer, string $paymentIntentId, ?int $userId): Order
    {
        return DB::transaction(function () use ($lines, $totalCents, $customer, $paymentIntentId, $userId) {
            $this->stock->reserve($lines);

            $order = Order::create([
                ...$customer,
                'user_id' => $userId,
                'total' => $totalCents / 100,
                'status' => 'pending',
                'stripe_payment_intent_id' => $paymentIntentId,
                'payment_status' => 'paid',
            ]);

            $order->items()->createMany($lines->all());

            return $order;
        });
    }
}
