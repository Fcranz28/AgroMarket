<?php

namespace App\Modules\Backoffice\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Models\Review;
use App\Modules\Orders\Enums\OrderStatus;
use App\Modules\Orders\Enums\PaymentStatus;
use App\Modules\Orders\Models\OrderItem;
use App\Modules\Shared\Money;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FarmerDashboardController extends Controller
{
    /**
     * Months shown in the sales chart, including the current one.
     */
    private const CHART_MONTHS = 12;

    public function index(): View
    {
        $user = Auth::user();

        if (! $user->isVerified()) {
            return view('farmer.pending');
        }

        $soldItems = OrderItem::query()
            ->whereHas('product', fn ($query) => $query->where('user_id', $user->id))
            ->whereHas('order', fn ($query) => $query
                ->where('payment_status', PaymentStatus::Paid)
                ->where('status', '!=', OrderStatus::Cancelled));

        $products = Product::ownedBy($user)->count();
        $sales = (clone $soldItems)->distinct()->count('order_id');
        $revenue = (float) (clone $soldItems)->selectRaw('COALESCE(SUM(price * quantity), 0) as revenue')->value('revenue');
        $averageRating = Review::whereHas('product', fn ($query) => $query->where('user_id', $user->id))->avg('rating');

        $recentProducts = Product::ownedBy($user)->latest()->take(5)->get();

        return view('farmer.dashboard', [
            'products' => $products,
            'sales' => $sales,
            'revenue' => $revenue,
            'averageRating' => $averageRating !== null ? (float) $averageRating : null,
            'monthlyRevenue' => $this->monthlyRevenue(clone $soldItems),
            'recentProducts' => $recentProducts,
        ]);
    }

    /**
     * Revenue of the last months, oldest first. Grouped in PHP so it works on any database.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<OrderItem>  $soldItems
     * @return list<array{label: string, cents: int, formatted: string}>
     */
    private function monthlyRevenue($soldItems): array
    {
        $firstMonth = Carbon::now()->startOfMonth()->subMonths(self::CHART_MONTHS - 1);

        $totals = $soldItems
            ->with('order:id,created_at')
            ->whereHas('order', fn ($query) => $query->where('created_at', '>=', $firstMonth))
            ->get(['order_id', 'price', 'quantity'])
            ->groupBy(fn (OrderItem $item) => $item->order->created_at->format('Y-m'))
            ->map(fn ($items) => $items->sum(fn (OrderItem $item) => Money::fromDecimal($item->price)->multiply($item->quantity)->cents()));

        return collect(range(0, self::CHART_MONTHS - 1))
            ->map(function (int $offset) use ($firstMonth, $totals) {
                $month = $firstMonth->copy()->addMonths($offset);
                $cents = (int) ($totals[$month->format('Y-m')] ?? 0);

                return [
                    'label' => ucfirst($month->translatedFormat('M Y')),
                    'cents' => $cents,
                    'formatted' => Money::fromCents($cents)->format(),
                ];
            })
            ->all();
    }
}
