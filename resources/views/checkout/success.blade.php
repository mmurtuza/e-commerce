@extends('layouts.app')
@section('title', __('Order Confirmed') . ' | ' . \App\Models\Setting::get('site_name', 'Dinajpur IT Park'))

@section('content')
<div class="max-w-2xl mx-auto px-4 py-16 text-center" x-data="{ showPopup: false }" x-init="setTimeout(() => showPopup = true, 300)">
    <!-- Confetti Style Animation -->
    <template x-if="showPopup">
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            
            <div class="bg-white rounded-[2rem] shadow-2xl p-10 max-w-sm w-full relative overflow-hidden"
                 @click.away="showPopup = false"
                 x-transition:enter="transition ease-out duration-500 delay-100"
                 x-transition:enter-start="opacity-0 scale-90 translate-y-8"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                
                <!-- Success Icon with Pulse -->
                <div class="w-24 h-24 bg-green-100 text-primary-600 rounded-full flex items-center justify-center mx-auto mb-6 relative">
                    <div class="absolute inset-0 rounded-full bg-green-200 animate-ping opacity-25"></div>
                    <svg class="w-12 h-12 relative" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <h2 class="text-3xl font-extrabold text-gray-900 mb-2">{{ __('Success!') }}</h2>
                <p class="text-gray-500 mb-8">{{ __('Your order has been placed successfully. Thank you for choosing us!') }}</p>

                <button @click="showPopup = false" class="w-full bg-primary-600 text-white py-4 rounded-2xl font-bold text-lg hover:bg-primary-700 transition-all transform hover:scale-[1.02] active:scale-95">
                    {{ __('Awesome!') }}
                </button>

                <!-- Decorative elements -->
                <div class="absolute -top-4 -right-4 w-16 h-16 bg-green-50 rounded-full opacity-50"></div>
                <div class="absolute -bottom-8 -left-8 w-24 h-24 bg-green-50 rounded-full opacity-50"></div>
            </div>
        </div>
    </template>

    <div class="text-7xl mb-6 animate-bounce">💻</div>
    <h1 class="text-4xl font-bold text-primary-600 mb-3">{{ __('Order Placed!') }}</h1>
    <p class="text-gray-600 mb-2">{{ __('Thank you for your order. We\'ll start preparing it right away!') }}</p>
    <p class="text-gray-500 text-sm mb-8">{{ __('Order #') }}<span class="font-bold text-gray-700">{{ $order->order_number }}</span></p>

    <div class="bg-white rounded-2xl shadow-sm p-6 text-left mb-6">
        <h2 class="font-bold text-gray-800 mb-4">{{ __('Order Details') }}</h2>
        @foreach($order->items as $item)
        <div class="flex justify-between items-center py-2 border-b last:border-0 text-sm">
            <span>{{ $item->product_name }} x{{ $item->quantity }}</span>
            <span class="font-semibold">৳{{ number_format($item->total_price, 0) }}</span>
        </div>
        @endforeach
        <div class="flex justify-between font-bold text-gray-800 mt-4 pt-2">
            <span>{{ __('Total') }}</span>
            <span>৳{{ number_format($order->total, 0) }}</span>
        </div>
    </div>

    <!-- Timeline -->
    <div class="bg-white rounded-2xl shadow-sm p-6 text-left mb-8">
        <h2 class="font-bold text-gray-800 mb-4">{{ __('What Happens Next?') }}</h2>
        <div class="space-y-3">
            @foreach(['Order Confirmed' => true, 'Being Prepared' => false, 'Shipped' => false, 'Delivered' => false] as $step => $done)
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $done ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-400' }} text-sm font-bold shrink-0">{{ $done ? '✓' : '·' }}</div>
                <span class="{{ $done ? 'text-gray-800 font-semibold' : 'text-gray-400' }} text-sm">{{ __($step) }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="flex gap-4 justify-center">
        <a href="{{ route('customer.order.show', $order->order_number) }}" class="bg-primary-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary-400 transition">{{ __('Track Order') }}</a>
        <a href="{{ route('shop.index') }}" class="border border-primary-600 text-primary-600 px-6 py-3 rounded-xl font-semibold hover:bg-primary-50 transition">{{ __('Continue Shopping') }}</a>
    </div>
</div>
@endsection
