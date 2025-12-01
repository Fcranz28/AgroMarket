<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Added
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function __construct()
    {
        // NOTE: No auth middleware to allow guest checkout
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * Create a payment intent
     */
    public function createPaymentIntent(Request $request)
    {
        try {
            $request->validate([
                'cart' => 'required|array',
                'cart.*.id' => 'required|exists:products,id',
                'cart.*.cantidad' => 'required|integer|min:1'
            ]);

            // Calculate total from database
            $amount = 0;
            foreach ($request->cart as $item) {
                $product = Product::find($item['id']);
                if ($product) {
                    $amount += $product->price * $item['cantidad'];
                }
            }

            // Enforce minimum amount for Stripe (approx $0.50 USD)
            // S/. 2.00 is safely above the limit
            if ($amount < 2.00) {
                return response()->json([
                    'error' => 'El monto mínimo de compra para pagos con tarjeta es de S/. 2.00. Por favor, agregue más productos a su carrito.'
                ], 400);
            }
            
            // Create Stripe Payment Intent
            $paymentIntent = PaymentIntent::create([
                'amount' => (int)($amount * 100), // Convert to cents
                'currency' => 'pen', // Peruvian Sol
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => [
                    'user_id' => Auth::id(),
                    'cart_items' => count($request->cart)
                ]
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'paymentIntentId' => $paymentIntent->id
            ]);

        } catch (\Stripe\Exception\InvalidRequestException $e) {
            \Log::error('Stripe Invalid Request: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error en la solicitud de pago: ' . $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            \Log::error('Payment Intent creation failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al iniciar el sistema de pagos. Por favor intente nuevamente.'
            ], 500);
        }
    }

    /**
     * Process payment and create order
     */
    public function processPayment(Request $request)
    {
        try {
            $request->validate([
                'payment_intent_id' => 'required|string',
                'cart' => 'required|array',
                'cart.*.id' => 'required|exists:products,id',
                'cart.*.cantidad' => 'required|integer|min:1',
                'shipping_address' => 'required|string',
                'phone' => 'required|string',
                // Guest/Customer Info Validation
                'guest_name' => 'required|string',
                'guest_lastname' => 'required|string',
                'guest_email' => 'required|email',
                'document_type' => 'required|string',
                'document_number' => 'required|string',
            ]);

            // Verify payment intent
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($paymentIntent->status !== 'succeeded') {
                return response()->json([
                    'error' => 'El pago no se ha completado'
                ], 400);
            }

            return DB::transaction(function () use ($request) {
                // Calculate total and verify stock
                $total = 0;
                $orderItemsData = [];

                foreach ($request->cart as $item) {
                    // Lock the product row for update to prevent race conditions
                    $product = Product::where('id', $item['id'])->lockForUpdate()->first();

                    if (!$product) {
                        throw new \Exception("Producto no encontrado: ID " . $item['id']);
                    }

                    if ($product->stock < $item['cantidad']) {
                        throw new \Exception("Stock insuficiente para el producto: " . $product->name);
                    }

                    // Decrement stock
                    $product->decrement('stock', $item['cantidad']);

                    $total += $product->price * $item['cantidad'];
                    
                    $orderItemsData[] = [
                        'product_id' => $product->id,
                        'quantity' => $item['cantidad'],
                        'price' => $product->price
                    ];
                }

                // Create order
                $order = Order::create([
                    'user_id' => Auth::id(), // Nullable now
                    'total' => $total,
                    'status' => 'pending',
                    'shipping_address' => $request->shipping_address,
                    'phone' => $request->phone,
                    'stripe_payment_intent_id' => $request->payment_intent_id,
                    'payment_status' => 'paid',
                    // Guest fields
                    'guest_name' => $request->guest_name,
                    'guest_lastname' => $request->guest_lastname,
                    'guest_email' => $request->guest_email,
                    'document_type' => $request->document_type,
                    'document_number' => $request->document_number,
                ]);

                // Create order items
                foreach ($orderItemsData as $itemData) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $itemData['product_id'],
                        'quantity' => $itemData['quantity'],
                        'price' => $itemData['price']
                    ]);
                }

                // Generate electronic invoice
                try {
                    $invoiceService = new \App\Services\InvoiceService();
                    $invoice = $invoiceService->generateInvoice($order);
                    \Log::info('Invoice generated successfully', [
                        'order_id' => $order->id,
                        'invoice_id' => $invoice->id,
                        'invoice_number' => $invoice->numero_completo
                    ]);
                } catch (\Exception $e) {
                    // Don't fail the order if invoice generation fails
                    \Log::error('Invoice generation failed', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                    // Continue with order processing
                }

                // Determine redirect URL
                $redirectUrl = Auth::check() ? route('orders.show', $order) : route('home'); // Using model for route binding

                return response()->json([
                    'success' => true,
                    'order_id' => $order->id,
                    'redirect' => $redirectUrl
                ]);
            });

        } catch (\Exception $e) {
            \Log::error('Payment processing failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al procesar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Stripe webhook handler
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            if ($webhookSecret) {
                $event = \Stripe\Webhook::constructEvent(
                    $payload, $sigHeader, $webhookSecret
                );
            } else {
                $event = json_decode($payload);
            }

            // Handle the event
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    \Log::info('Payment succeeded: ' . $paymentIntent->id);
                    break;
                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    \Log::error('Payment failed: ' . $paymentIntent->id);
                    break;
                default:
                    \Log::info('Unhandled event type: ' . $event->type);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            \Log::error('Webhook error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
