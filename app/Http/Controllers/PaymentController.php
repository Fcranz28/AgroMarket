<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * Create a payment intent
     */
    public function createPaymentIntent(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:1',
                'cart' => 'required|array'
            ]);

            $amount = $request->amount;
            
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

        } catch (\Exception $e) {
            \Log::error('Payment Intent creation failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al crear la intención de pago: ' . $e->getMessage()
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
                'shipping_address' => 'required|string',
                'phone' => 'required|string'
            ]);

            // Verify payment intent
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($paymentIntent->status !== 'succeeded') {
                return response()->json([
                    'error' => 'El pago no se ha completado'
                ], 400);
            }

            // Calculate total
            $total = 0;
            foreach ($request->cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'total' => $total,
                'status' => 'pending',
                'shipping_address' => $request->shipping_address,
                'phone' => $request->phone,
                'stripe_payment_intent_id' => $request->payment_intent_id,
                'payment_status' => 'paid'
            ]);

            // Create order items
            foreach ($request->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
            }

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'redirect' => route('orders.show', $order->id)
            ]);

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
