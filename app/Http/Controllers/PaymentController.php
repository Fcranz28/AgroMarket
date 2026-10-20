<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Http\Requests\CreatePaymentIntentRequest;
use App\Http\Requests\ProcessPaymentRequest;
use App\Models\Invoice;
use App\Models\Order;
use App\Services\CartPricingService;
use App\Services\InvoiceService;
use App\Services\PaymentGateway;
use App\Services\StockService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Throwable;
use UnexpectedValueException;

class PaymentController extends Controller
{
    /**
     * Minimum card payment accepted by Stripe for PEN, in cents.
     */
    private const MINIMUM_AMOUNT_CENTS = 200;

    public function __construct(
        private PaymentGateway $gateway,
        private CartPricingService $pricing,
        private StockService $stock,
    ) {}

    /**
     * Create a payment intent for the server-side priced cart.
     */
    public function createPaymentIntent(CreatePaymentIntentRequest $request): JsonResponse
    {
        $pricedCart = $this->pricing->price($request->validated('cart'));

        if ($pricedCart['total_cents'] < self::MINIMUM_AMOUNT_CENTS) {
            return response()->json([
                'error' => 'El monto mínimo de compra para pagos con tarjeta es de S/. 2.00. Por favor, agregue más productos a su carrito.',
            ], 400);
        }

        try {
            $paymentIntent = $this->gateway->createIntent($pricedCart['total_cents'], [
                'user_id' => Auth::id(),
                'cart_hash' => $pricedCart['hash'],
            ]);
        } catch (ApiErrorException $exception) {
            Log::error('Stripe payment intent creation failed', ['error' => $exception->getMessage()]);

            return response()->json(['error' => 'Error al iniciar el sistema de pagos. Por favor intente nuevamente.'], 502);
        }

        return response()->json([
            'clientSecret' => $paymentIntent->client_secret,
            'paymentIntentId' => $paymentIntent->id,
        ]);
    }

    /**
     * Verify a succeeded payment intent against the cart and create the order.
     */
    public function processPayment(ProcessPaymentRequest $request): JsonResponse
    {
        $paymentIntentId = $request->validated('payment_intent_id');

        if (Order::where('stripe_payment_intent_id', $paymentIntentId)->exists()) {
            return response()->json(['error' => 'Este pago ya fue registrado.'], 409);
        }

        try {
            $paymentIntent = $this->gateway->retrieveIntent($paymentIntentId);
        } catch (ApiErrorException $exception) {
            Log::warning('Stripe payment intent lookup failed', ['payment_intent' => $paymentIntentId, 'error' => $exception->getMessage()]);

            return response()->json(['error' => 'No se pudo verificar el pago.'], 422);
        }

        if ($paymentIntent->status !== 'succeeded') {
            return response()->json(['error' => 'El pago no se ha completado'], 400);
        }

        $pricedCart = $this->pricing->price($request->validated('cart'));

        if ($paymentIntent->amount !== $pricedCart['total_cents']
            || $paymentIntent->currency !== $this->gateway->currency()
            || ($paymentIntent->metadata['cart_hash'] ?? null) !== $pricedCart['hash']) {
            Log::warning('Payment intent does not match cart', ['payment_intent' => $paymentIntentId]);

            return response()->json(['error' => 'El pago no coincide con el contenido del carrito.'], 422);
        }

        try {
            $order = DB::transaction(fn () => $this->createOrder($request, $pricedCart['lines'], $pricedCart['total_cents']));
        } catch (InsufficientStockException $exception) {
            $this->refund($paymentIntentId);

            return response()->json(['error' => $exception->getMessage().' Se reembolsó tu pago.'], 409);
        } catch (UniqueConstraintViolationException) {
            return response()->json(['error' => 'Este pago ya fue registrado.'], 409);
        }

        $invoice = $this->generateInvoice($order);

        return response()->json([
            'success' => true,
            'order_id' => $order->uuid,
            'redirect' => Auth::check() ? route('orders.show', $order) : route('home'),
            'invoice_url' => $invoice && ! Auth::check()
                ? URL::temporarySignedRoute('invoice.guest-download', now()->addDays(7), $invoice)
                : null,
        ]);
    }

    /**
     * Stripe webhook handler. Only signed events are accepted.
     */
    public function webhook(Request $request): JsonResponse
    {
        $webhookSecret = config('services.stripe.webhook_secret');

        if (blank($webhookSecret)) {
            Log::error('Stripe webhook received but STRIPE_WEBHOOK_SECRET is not configured');

            return response()->json(['error' => 'Webhook not configured'], 400);
        }

        try {
            $event = Webhook::constructEvent($request->getContent(), (string) $request->header('Stripe-Signature'), $webhookSecret);
        } catch (SignatureVerificationException|UnexpectedValueException) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $paymentIntent = $event->data->object;

        match ($event->type) {
            'payment_intent.succeeded' => Order::where('stripe_payment_intent_id', $paymentIntent->id)
                ->update(['payment_status' => 'paid']),
            'payment_intent.payment_failed' => Log::warning('Stripe payment failed', ['payment_intent' => $paymentIntent->id]),
            default => null,
        };

        return response()->json(['status' => 'success']);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, array{product_id: int, product_unit_id: int|null, unit: string, quantity: int, price: string}>  $lines
     *
     * @throws InsufficientStockException
     */
    private function createOrder(ProcessPaymentRequest $request, $lines, int $totalCents): Order
    {
        $this->stock->reserve($lines);

        $order = Order::create([
            'user_id' => Auth::id(),
            'total' => $totalCents / 100,
            'status' => 'pending',
            'shipping_address' => $request->validated('shipping_address'),
            'phone' => $request->validated('phone'),
            'stripe_payment_intent_id' => $request->validated('payment_intent_id'),
            'payment_status' => 'paid',
            'guest_name' => $request->validated('guest_name'),
            'guest_lastname' => $request->validated('guest_lastname'),
            'guest_email' => $request->validated('guest_email'),
            'document_type' => $request->validated('document_type'),
            'document_number' => $request->validated('document_number'),
        ]);

        $order->items()->createMany($lines->all());

        return $order;
    }

    private function refund(string $paymentIntentId): void
    {
        try {
            $this->gateway->refund($paymentIntentId);
        } catch (Throwable $exception) {
            Log::critical('Automatic refund failed', ['payment_intent' => $paymentIntentId, 'error' => $exception->getMessage()]);
        }
    }

    /**
     * Invoice generation must never undo a paid order, so it runs after the order is committed.
     */
    private function generateInvoice(Order $order): ?Invoice
    {
        try {
            return app(InvoiceService::class)->generateInvoice($order);
        } catch (Throwable $exception) {
            Log::error('Invoice generation failed', ['order_id' => $order->id, 'error' => $exception->getMessage()]);

            return null;
        }
    }
}
