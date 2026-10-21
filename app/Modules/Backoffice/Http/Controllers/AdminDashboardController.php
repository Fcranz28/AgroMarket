<?php

namespace App\Modules\Backoffice\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Models\Product;
use App\Modules\Identity\Enums\UserRole;
use App\Modules\Identity\Models\User;
use App\Modules\Moderation\Enums\ReportStatus;
use App\Modules\Moderation\Models\Report;
use App\Modules\Orders\Enums\OrderStatus;
use App\Modules\Orders\Enums\PaymentStatus;
use App\Modules\Orders\Models\Order;
use App\Modules\Shared\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Months shown in the revenue chart, including the current one.
     */
    private const CHART_MONTHS = 12;

    public function index(): View
    {
        $paidOrders = Order::query()
            ->where('payment_status', PaymentStatus::Paid)
            ->where('status', '!=', OrderStatus::Cancelled);

        return view('admin.dashboard', [
            'totalUsers' => User::where('role', UserRole::User)->count(),
            'totalFarmers' => User::farmers()->count(),
            'pendingFarmers' => User::pendingVerification()->latest()->take(5)->get(['id', 'name', 'email', 'created_at']),
            'pendingFarmersCount' => User::pendingVerification()->count(),
            'pendingReportsCount' => Report::where('status', ReportStatus::Pending)->count(),
            'totalProducts' => Product::count(),
            'totalOrders' => (clone $paidOrders)->count(),
            'totalRevenue' => Money::fromDecimal((string) (clone $paidOrders)->sum('total')),
            'monthlyRevenue' => $this->monthlyRevenue(clone $paidOrders),
        ]);
    }

    /**
     * Revenue of the last months, oldest first. Grouped in PHP so it works on any database.
     *
     * @param  Builder<Order>  $paidOrders
     * @return list<array{label: string, cents: int, formatted: string}>
     */
    private function monthlyRevenue(Builder $paidOrders): array
    {
        $firstMonth = Carbon::now()->startOfMonth()->subMonths(self::CHART_MONTHS - 1);

        $totals = $paidOrders
            ->where('created_at', '>=', $firstMonth)
            ->get(['total', 'created_at'])
            ->groupBy(fn (Order $order) => $order->created_at->format('Y-m'))
            ->map(fn ($orders) => $orders->sum(fn (Order $order) => Money::fromDecimal((string) $order->total)->cents()));

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
