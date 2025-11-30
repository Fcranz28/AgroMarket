<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display user's orders, profile and addresses
     */
    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->where('payment_status', 'paid')
            ->with(['items.product', 'invoice'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('orders.index', compact('user', 'orders'));
    }

    /**
     * Show specific order
     */
    public function show(Order $order)
    {
        // Make sure user owns this order
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items.product', 'invoice']);

        return view('orders.show', compact('order'));
    }

    /**
     * Display farmer's orders (where they are the seller)
     */
    public function farmerOrders()
    {
        $user = Auth::user();
        
        // Get all orders that contain products owned by this farmer
        $orders = Order::whereHas('items.product', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['items.product', 'user', 'invoice'])
            ->where('payment_status', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();

        // Filter to only show pending orders (not delivered/cancelled)
        $pendingOrders = $orders->filter(function ($order) {
            return in_array($order->status, ['pending', 'processing', 'shipped']);
        });

        return view('farmer.orders', compact('pendingOrders', 'orders'));
    }

    /**
     * Update order status
     */
    public function updateStatus(\Illuminate\Http\Request $request, Order $order)
    {
        // Verify user is a farmer
        if (!Auth::user()->isFarmer()) {
            abort(403, 'Acceso denegado. Solo los agricultores pueden actualizar el estado.');
        }

        // Verify that the order contains at least one product belonging to this farmer
        $hasFarmerProducts = $order->items()->whereHas('product', function ($query) {
            $query->where('user_id', Auth::id());
        })->exists();

        if (!$hasFarmerProducts) {
            abort(403, 'No tienes permiso para actualizar este pedido.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente'
        ]);
    }
    /**
     * Show order details for farmer
     */
    public function farmerShow(Order $order)
    {
        // Verify user is a farmer
        if (!Auth::user()->isFarmer()) {
            abort(403);
        }

        // Verify that the order contains products from this farmer
        $hasFarmerProducts = $order->items()->whereHas('product', function ($query) {
            $query->where('user_id', Auth::id());
        })->exists();

        if (!$hasFarmerProducts) {
            abort(403);
        }

        $order->load(['items.product', 'invoice']);
        
        return view('orders.show', compact('order'));
    }
}
