<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\PaymentGateway;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class OrderController extends Controller
{
    /**
     * Display user's orders, profile and addresses
     */
    public function index(): View
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->where('payment_status', 'paid')
            ->with(['items.product', 'invoice'])
            ->latest()
            ->get();

        return view('orders.index', compact('user', 'orders'));
    }

    /**
     * Show specific order
     */
    public function show(Order $order): View
    {
        abort_if($order->user_id === null || $order->user_id !== Auth::id(), 403);

        $order->load(['items.product', 'invoice']);

        return view('orders.show', compact('order'));
    }

    /**
     * Display farmer's orders (where they are the seller)
     */
    public function farmerOrders(): View
    {
        $user = Auth::user();

        $orders = Order::whereHas('items.product', fn ($query) => $query->where('user_id', $user->id))
            ->with(['items.product', 'user', 'invoice'])
            ->where('payment_status', 'paid')
            ->latest()
            ->get();

        $pendingOrders = $orders->reject(fn (Order $order) => $order->isFinal());

        return view('farmer.orders', compact('pendingOrders', 'orders'));
    }

    /**
     * Update order status. Cancelling returns the stock and refunds the payment.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order, StockService $stock, PaymentGateway $gateway): JsonResponse
    {
        if ($order->isFinal()) {
            return response()->json(['success' => false, 'message' => 'Este pedido ya fue cerrado y no puede cambiar de estado.'], 422);
        }

        $status = $request->validated('status');

        if ($status !== Order::STATUS_CANCELLED) {
            $order->update(['status' => $status]);

            return response()->json(['success' => true, 'message' => 'Estado actualizado correctamente']);
        }

        DB::transaction(function () use ($order, $stock) {
            $stock->release($order->items->map(fn ($item) => [
                'product_id' => $item->product_id,
                'product_unit_id' => $item->product_unit_id,
                'quantity' => $item->quantity,
            ]));

            $order->update(['status' => Order::STATUS_CANCELLED]);
        });

        if ($order->payment_status === 'paid' && $order->stripe_payment_intent_id) {
            try {
                $gateway->refund($order->stripe_payment_intent_id);
                $order->update(['payment_status' => 'refunded']);
            } catch (Throwable $exception) {
                Log::critical('Refund after cancellation failed', ['order_id' => $order->id, 'error' => $exception->getMessage()]);

                return response()->json([
                    'success' => true,
                    'message' => 'Pedido cancelado. El reembolso no se pudo procesar automáticamente; el equipo lo revisará.',
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Pedido cancelado y reembolsado.']);
    }

    /**
     * Show order details for farmer
     */
    public function farmerShow(Order $order): View
    {
        abort_unless(Auth::user()->isFarmer() && $order->hasItemsSoldBy(Auth::user()), 403);

        $order->load(['items.product', 'invoice']);

        return view('orders.show', compact('order'));
    }
}
