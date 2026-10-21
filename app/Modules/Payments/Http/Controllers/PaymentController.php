<?php

namespace App\Modules\Payments\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Orders\Services\SessionCart;
use App\Modules\Payments\Actions\CompleteCheckout;
use App\Modules\Payments\Actions\CreateCheckoutIntent;
use App\Modules\Payments\Exceptions\CheckoutException;
use App\Modules\Payments\Http\Requests\CreatePaymentIntentRequest;
use App\Modules\Payments\Http\Requests\ProcessPaymentRequest;
use App\Modules\Payments\Jobs\HandleStripeEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class PaymentController extends Controller
{
    public function __construct(
        private CreateCheckoutIntent $createIntent,
        private CompleteCheckout $completeCheckout,
        private SessionCart $cart,
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
        try {
            $paymentIntent = $this->createIntent->execute($request->validated('cart'), Auth::id());
        } catch (CheckoutException $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->status);
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
                new OA\Property(property: 'invoice_url', type: 'string', format: 'uri', nullable: true, description: 'Solo para invitados: URL firmada (7 días) del comprobante. Muestra "en proceso" hasta que el worker de colas lo genera.'),
            ])),
            new OA\Response(response: 400, description: 'El pago no está completado', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 409, description: 'Pago ya registrado o sin stock (se reembolsa)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 422, description: 'Datos inválidos o el pago no coincide con el carrito', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(ref: '#/components/responses/TooManyRequests', response: 429),
        ],
    )]
    public function processPayment(ProcessPaymentRequest $request): JsonResponse
    {
        try {
            $order = $this->completeCheckout->execute(
                $request->validated('payment_intent_id'),
                $request->validated('cart'),
                $request->safe()->only(['shipping_address', 'phone', 'guest_name', 'guest_lastname', 'guest_email', 'document_type', 'document_number']),
                Auth::id(),
            );
        } catch (CheckoutException $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->status);
        }

        $this->cart->clear();

        // The receipt is issued in the background (OrderPlaced → Invoicing); guests get a
        // signed link that waits until it is ready.
        return response()->json([
            'success' => true,
            'order_id' => $order->uuid,
            'redirect' => Auth::check() ? route('orders.show', $order) : route('home'),
            'invoice_url' => Auth::check() ? null : CompleteCheckout::guestInvoiceUrl($order),
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

        HandleStripeEvent::dispatch($event->type, (string) $event->data->object->id);

        return response()->json(['status' => 'success']);
    }
}
