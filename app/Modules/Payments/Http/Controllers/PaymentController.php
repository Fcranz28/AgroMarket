<?php

namespace App\Modules\Payments\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Exceptions\InsufficientStockException;
use App\Modules\Invoicing\Models\Invoice;
use App\Modules\Invoicing\Services\InvoiceService;
use App\Modules\Orders\Actions\PlaceOrderAction;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Services\CartPricingService;
use App\Modules\Payments\Http\Requests\CreatePaymentIntentRequest;
use App\Modules\Payments\Http\Requests\ProcessPaymentRequest;
use App\Modules\Payments\Services\PaymentGateway;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use OpenApi\Attributes as OA;
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
        private PlaceOrderAction $placeOrder,
    ) {}

    /**
     * Create a payment intent for the server-side priced cart.
     */
    #[OA\Post(
        path: '/payment/create-intent',
        summary: 'Crear el PaymentIntent de Stripe',
        description: 'El total se calcula en el servidor a partir del carrito (IGV incluido). El monto mínimo es S/. 2.00.',
        security: [['csrf' => []]],
        tags: ['Pagos'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['cart'], properties: [
            new OA\Property(property: 'cart', type: 'array', minItems: 1, maxItems: 50, items: new OA\Items(ref: '#/components/schemas/CartLine')),
        ])),
        responses: [
            new OA\Response(response: 200, description: 'PaymentIntent creado', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'clientSecret', type: 'string', example: 'pi_123_secret_456'),
                new OA\Property(property: 'paymentIntentId', type: 'string', example: 'pi_123'),
            ])),
            new OA\Response(response: 400, description: 'Monto menor al mínimo', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(ref: '#/components/responses/ValidationFailed', response: 422),
            new OA\Response(ref: '#/components/responses/TooManyRequests', response: 429),
            new OA\Response(response: 502, description: 'Stripe no respondió', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ],
    )]
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
    #[OA\Post(
        path: '/payment/process',
        summary: 'Confirmar el pago y crear el pedido',
        description: 'Verifica que el PaymentIntent esté pagado y coincida con el carrito (monto, moneda y hash), descuenta el stock y genera la factura. Si falta stock, el pago se reembolsa automáticamente.',
        security: [['csrf' => []]],
        tags: ['Pagos'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['payment_intent_id', 'cart', 'shipping_address', 'phone', 'guest_name', 'guest_lastname', 'guest_email', 'document_type', 'document_number'],
            properties: [
                new OA\Property(property: 'payment_intent_id', type: 'string', example: 'pi_123'),
                new OA\Property(property: 'cart', type: 'array', items: new OA\Items(ref: '#/components/schemas/CartLine')),
                new OA\Property(property: 'shipping_address', type: 'string', maxLength: 500, example: 'Av. Arequipa 1234, Lima'),
                new OA\Property(property: 'phone', type: 'string', maxLength: 20, example: '987654321'),
                new OA\Property(property: 'guest_name', type: 'string', example: 'Ana'),
                new OA\Property(property: 'guest_lastname', type: 'string', example: 'Quispe'),
                new OA\Property(property: 'guest_email', type: 'string', format: 'email', example: 'ana@example.com'),
                new OA\Property(property: 'document_type', type: 'string', enum: ['DNI', 'RUC', 'Pasaporte', 'Carnet de Extranjeria']),
                new OA\Property(property: 'document_number', type: 'string', example: '12345678'),
            ],
        )),
        responses: [
            new OA\Response(response: 200, description: 'Pedido creado', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(property: 'order_id', type: 'string', format: 'uuid'),
                new OA\Property(property: 'redirect', type: 'string', format: 'uri'),
                new OA\Property(property: 'invoice_url', type: 'string', format: 'uri', nullable: true, description: 'Solo para invitados: URL firmada (7 días) del comprobante'),
            ])),
            new OA\Response(response: 400, description: 'El pago no está completado', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 409, description: 'Pago ya registrado o sin stock (se reembolsa)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 422, description: 'Datos inválidos o el pago no coincide con el carrito', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(ref: '#/components/responses/TooManyRequests', response: 429),
        ],
    )]
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
            $order = $this->placeOrder->execute(
                $pricedCart['lines'],
                $pricedCart['total_cents'],
                $request->safe()->only(['shipping_address', 'phone', 'guest_name', 'guest_lastname', 'guest_email', 'document_type', 'document_number']),
                $paymentIntentId,
                Auth::id(),
            );
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
    #[OA\Post(
        path: '/stripe/webhook',
        summary: 'Webhook de Stripe',
        description: 'Solo para Stripe. Exige la cabecera Stripe-Signature firmada con STRIPE_WEBHOOK_SECRET. Marca el pedido como pagado en payment_intent.succeeded.',
        tags: ['Pagos'],
        parameters: [new OA\Parameter(name: 'Stripe-Signature', in: 'header', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: true, description: 'Evento de Stripe', content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 200, description: 'Evento procesado', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
            ])),
            new OA\Response(response: 400, description: 'Firma inválida o webhook sin configurar', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ],
    )]
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
