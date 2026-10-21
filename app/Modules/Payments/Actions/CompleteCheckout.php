<?php

namespace App\Modules\Payments\Actions;

use App\Modules\Catalog\Exceptions\InsufficientStockException;
use App\Modules\Orders\Actions\PlaceOrderAction;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Services\CartPricingService;
use App\Modules\Payments\Exceptions\CheckoutException;
use App\Modules\Payments\Jobs\RefundPayment;
use App\Modules\Payments\Services\PaymentGateway;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Stripe\Exception\ApiErrorException;

/**
 * Verify a succeeded payment intent against the cart and create the order. If the stock
 * ran out in the meantime, the payment is refunded in the background.
 */
class CompleteCheckout
{
    public function __construct(
        private PaymentGateway $gateway,
        private CartPricingService $pricing,
        private PlaceOrderAction $placeOrder,
    ) {}

    /**
     * @param  array<int, array{id: int|string, unit_id?: int|string|null, cantidad: int|string}>  $cart
     * @param  array{shipping_address: string, phone: string, guest_name: string, guest_lastname: string, guest_email: string, document_type: string, document_number: string}  $customer
     *
     * @throws CheckoutException
     */
    public function execute(string $paymentIntentId, array $cart, array $customer, ?int $userId): Order
    {
        if (Order::where('stripe_payment_intent_id', $paymentIntentId)->exists()) {
            throw new CheckoutException('Este pago ya fue registrado.', 409);
        }

        try {
            $paymentIntent = $this->gateway->retrieveIntent($paymentIntentId);
        } catch (ApiErrorException $exception) {
            Log::warning('Stripe payment intent lookup failed', ['payment_intent' => $paymentIntentId, 'error' => $exception->getMessage()]);

            throw new CheckoutException('No se pudo verificar el pago.', 422);
        }

        if ($paymentIntent->status !== 'succeeded') {
            throw new CheckoutException('El pago no se ha completado', 400);
        }

        $pricedCart = $this->pricing->price($cart);

        if ($paymentIntent->amount !== $pricedCart['total_cents']
            || $paymentIntent->currency !== $this->gateway->currency()
            || ($paymentIntent->metadata['cart_hash'] ?? null) !== $pricedCart['hash']) {
            Log::warning('Payment intent does not match cart', ['payment_intent' => $paymentIntentId]);

            throw new CheckoutException('El pago no coincide con el contenido del carrito.', 422);
        }

        try {
            return $this->placeOrder->execute($pricedCart['lines'], $pricedCart['total_cents'], $customer, $paymentIntentId, $userId);
        } catch (InsufficientStockException $exception) {
            RefundPayment::dispatch($paymentIntentId);

            throw new CheckoutException($exception->getMessage().' Reembolsaremos tu pago automáticamente.', 409);
        } catch (UniqueConstraintViolationException) {
            throw new CheckoutException('Este pago ya fue registrado.', 409);
        }
    }

    /**
     * Signed link a guest uses to get the receipt, which is issued in the background
     * (OrderPlaced → Invoicing): it shows a waiting page until it is ready.
     */
    public static function guestInvoiceUrl(Order $order): string
    {
        return URL::temporarySignedRoute('orders.guest-invoice', now()->addDays(7), ['order' => $order]);
    }
}
