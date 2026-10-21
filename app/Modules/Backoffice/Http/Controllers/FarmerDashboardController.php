<?php

namespace App\Modules\Backoffice\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FarmerDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        if (! $user->isVerified()) {
            return view('farmer.pending');
        }

        $soldItems = OrderItem::query()
            ->whereHas('product', fn ($query) => $query->where('user_id', $user->id))
            ->whereHas('order', fn ($query) => $query
                ->where('payment_status', 'paid')
                ->where('status', '!=', Order::STATUS_CANCELLED));

        $products = $user->products()->count();
        $sales = (clone $soldItems)->distinct()->count('order_id');
        $revenue = (float) (clone $soldItems)->selectRaw('COALESCE(SUM(price * quantity), 0) as revenue')->value('revenue');

        $recentProducts = $user->products()->latest()->take(5)->get();

        return view('farmer.dashboard', compact('products', 'sales', 'revenue', 'recentProducts'));
    }
}
