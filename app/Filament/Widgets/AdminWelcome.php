<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\CouponResource;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\ProductResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class AdminWelcome extends Widget
{
    protected static string $view = 'filament.widgets.admin-welcome';

    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $today = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();

        return [
            'adminName' => auth('admin')->user()?->name ?? 'Admin',
            'todayOrders' => Order::whereDate('created_at', $today)->count(),
            'pendingOrders' => Order::where(
                'status',
                OrderStatus::Pending,
            )->count(),
            'monthlyRevenue' => Order::where(
                'payment_status',
                PaymentStatus::Paid,
            )
                ->where('created_at', '>=', $monthStart)
                ->sum('total'),
            'totalCustomers' => User::count(),
            'lowStockProducts' => Product::whereColumn(
                'stock_quantity',
                '<=',
                'low_stock_threshold',
            )
                ->where('is_active', true)
                ->count(),
            'quickActions' => [
                [
                    'label' => 'Manage Orders',
                    'description' => 'Review, update, and fulfill customer orders',
                    'icon' => 'heroicon-o-shopping-bag',
                    'url' => OrderResource::getUrl('index'),
                    'color' => 'emerald',
                ],
                [
                    'label' => 'Add Product',
                    'description' => 'Create a new plant, pot, seed, or accessory',
                    'icon' => 'heroicon-o-sparkles',
                    'url' => ProductResource::getUrl('create'),
                    'color' => 'green',
                ],
                [
                    'label' => 'Categories',
                    'description' => 'Organize products for easier shopping',
                    'icon' => 'heroicon-o-squares-2x2',
                    'url' => CategoryResource::getUrl('index'),
                    'color' => 'teal',
                ],
                [
                    'label' => 'Coupons',
                    'description' => 'Create discounts and marketing offers',
                    'icon' => 'heroicon-o-ticket',
                    'url' => CouponResource::getUrl('index'),
                    'color' => 'lime',
                ],
            ],
        ];
    }
}
