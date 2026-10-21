<?php

namespace App\Modules\Payments\Actions;

use App\Modules\Orders\Services\CartPricingService;
use App\Modules\Payments\Exceptions\CheckoutException;
use App\Modules\Payments\Services\PaymentGateway;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;

/**
 * Create the Stripe payment intent for a cart priced on the server. The cart hash goes in
 * the metadata so CompleteCheckout can check the payment matches the same cart.
 */
class CreateCheckoutIntent
{
    /**
     * Minimum card payment accepted by Stripe for PEN, in cents.
     */
    public const MINIMUM_AMOUNT_CENTS = 200;

    public function __construct(
        private PaymentGateway $gateway,
        private CartPricingService $pricing,
    ) {}

    /**
     * @param  array<int, array{id: int|string, unit_id?: int|string|null, cantidad: int|string}>  $cart
     *
     * @throws CheckoutException
     */
    public function execute(array $cart, ?int $userId): PaymentIntent
    {
        $pricedCart = $this->pricing->price($cart);

        if ($pricedCart['total_cents'] < self::MINIMUM_AMOUNT_CENTS) {
            throw new CheckoutException('El monto mínimo de compra para pagos con tarjeta es de S/. 2.00. Por favor, agregue más productos a su carrito.', 400);
        }

        try {
            return $this->gateway->createIntent($pricedCart['total_cents'], [
                'user_id' => $userId,
                'cart_hash' => $pricedCart['hash'],
            ]);
        } catch (ApiErrorException $exception) {
            Log::error('Stripe payment intent creation failed', ['error' => $exception->getMessage()]);

            throw new CheckoutException('Error al iniciar el sistema de pagos. Por favor intente nuevamente.', 502);
        }
    }
}
