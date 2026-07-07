<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $today = Carbon::today();

        $todayOrdersCount = Order::whereDate('created_at', $today)->count();

        $todayRevenue = Order::whereDate('created_at', $today)
            ->where('payment_status', PaymentStatus::Paid)
            ->sum('total');

        $yesterdayRevenue = Order::whereDate('created_at', $today->copy()->subDay())
            ->where('payment_status', PaymentStatus::Paid)
            ->sum('total');

        $revenueChange = $yesterdayRevenue > 0
            ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1)
            : 0;

        $totalCustomers = User::count();

        $newCustomersThisMonth = User::whereMonth('created_at', $today->month)
            ->whereYear('created_at', $today->year)
            ->count();

        $pendingOrders = Order::where('status', OrderStatus::Pending)->count();

        $lastWeekPending = Order::where('status', OrderStatus::Pending)
            ->where('created_at', '<', $today->copy()->subWeek())
            ->count();

        return [
            Stat::make("Today's Orders", (string) $todayOrdersCount)
                ->description('Orders placed today')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary')
                ->chart(
                    Order::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                        ->whereDate('created_at', '>=', $today->copy()->subDays(6))
                        ->groupBy('date')
                        ->orderBy('date')
                        ->pluck('count')
                        ->toArray()
                ),

            Stat::make("Today's Revenue", '৳'.number_format((float) $todayRevenue, 2))
                ->description($revenueChange >= 0
                    ? "{$revenueChange}% increase from yesterday"
                    : abs($revenueChange).'% decrease from yesterday')
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger')
                ->chart(
                    Order::selectRaw('DATE(created_at) as date, SUM(total) as revenue')
                        ->where('payment_status', PaymentStatus::Paid)
                        ->whereDate('created_at', '>=', $today->copy()->subDays(6))
                        ->groupBy('date')
                        ->orderBy('date')
                        ->pluck('revenue')
                        ->map(fn ($v) => (float) $v)
                        ->toArray()
                ),

            Stat::make('Total Customers', number_format($totalCustomers))
                ->description("{$newCustomersThisMonth} new this month")
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info')
                ->chart(
                    User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                        ->whereDate('created_at', '>=', $today->copy()->subDays(6))
                        ->groupBy('date')
                        ->orderBy('date')
                        ->pluck('count')
                        ->toArray()
                ),

            Stat::make('Pending Orders', (string) $pendingOrders)
                ->description($pendingOrders > $lastWeekPending
                    ? 'More than last week — action needed'
                    : 'On par with last week')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 10 ? 'warning' : 'gray'),
        ];
    }
}
