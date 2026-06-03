@extends('layouts.app')
@section('title', __('general.dashboard') . ' | ' . \App\Models\Setting::get('site_name', 'Dinajpur IT Park'))

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ __('general.welcome_back') }}, {{ auth()->user()->name }}! 💻</h1>
    <p class="text-gray-500 mb-8">{{ __('general.account_overview') }}</p>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl p-5 shadow-sm text-center">
            <div class="text-3xl font-bold text-primary-600">{{ auth()->user()->orders()->count() }}</div>
            <div class="text-sm text-gray-500 mt-1">{{ __('general.total_orders') }}</div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm text-center">
            <div class="text-3xl font-bold text-primary-400">৳{{ number_format(auth()->user()->total_spent, 0) }}</div>
            <div class="text-sm text-gray-500 mt-1">{{ __('general.total_spent') }}</div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm text-center">
            <div class="text-3xl font-bold text-orange-500">{{ auth()->user()->wishlists()->count() }}</div>
            <div class="text-sm text-gray-500 mt-1">{{ __('general.wishlist_items') }}</div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm text-center">
            <div class="text-3xl font-bold text-blue-500">{{ auth()->user()->reviews()->count() }}</div>
            <div class="text-sm text-gray-500 mt-1">{{ __('general.reviews') }}</div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        @foreach([
            ['route' => 'customer.orders', 'icon' => '📦', 'label' => __('general.my_orders')],
            ['route' => 'wishlist.index', 'icon' => '❤️', 'label' => __('general.wishlist')],
            ['route' => 'customer.addresses', 'icon' => '📍', 'label' => __('general.addresses')],
            ['route' => 'customer.profile', 'icon' => '👤', 'label' => __('general.profile')],
        ] as $link)
        <a href="{{ route($link['route']) }}" class="bg-white rounded-2xl p-5 shadow-sm text-center hover:shadow-md hover:-translate-y-1 transition-all duration-300">
            <div class="text-3xl mb-2">{{ $link['icon'] }}</div>
            <div class="text-sm font-medium text-gray-700">{{ $link['label'] }}</div>
        </a>
        @endforeach
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-5">
            <h2 class="font-bold text-gray-800 text-lg">{{ __('general.recent_orders') }}</h2>
            <a href="{{ route('customer.orders') }}" class="text-primary-600 text-sm hover:text-primary-400">{{ __('general.view_all') }} →</a>
        </div>
        @if($recentOrders->isEmpty())
        <div class="text-center py-10 text-gray-500">
            <div class="text-4xl mb-3">📦</div>
            <p>{{ __('general.no_orders_yet') }} <a href="{{ route('shop.index') }}" class="text-primary-600">{{ __('general.start_shopping_exclamation') }}</a></p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="pb-3">{{ __('general.order_num') }}</th>
                        <th class="pb-3">{{ __('general.date') }}</th>
                        <th class="pb-3">{{ __('general.status') }}</th>
                        <th class="pb-3">{{ __('general.total') }}</th>
                        <th class="pb-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($recentOrders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 font-semibold text-primary-600">{{ $order->order_number }}</td>
                        <td class="py-3 text-gray-500">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="py-3">
                            @php
                                $statusColor = $order->status->color();
                                $badgeClasses = match($statusColor) {
                                    'success' => 'bg-green-100 text-green-700',
                                    'warning' => 'bg-yellow-100 text-yellow-700',
                                    'danger'  => 'bg-red-100 text-red-700',
                                    'info'    => 'bg-blue-100 text-blue-700',
                                    'primary' => 'bg-indigo-100 text-indigo-700',
                                    default   => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $badgeClasses }}">
                                {{ $order->status->label() }}
                            </span>
                        </td>
                        <td class="py-3 font-bold">৳{{ number_format($order->total, 0) }}</td>
                        <td class="py-3 text-right">
                            <a href="{{ route('customer.order.show', $order->order_number) }}" class="text-primary-600 hover:text-primary-400">{{ __('general.view') }}</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
