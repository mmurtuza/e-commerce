@extends('layouts.app')
@section('title', __('general.cart') . ' | ' . \App\Models\Setting::get('site_name', 'Dinajpur IT Park'))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">{{ __('general.shopping_cart') }}</h1>

    @if($cart->items->isEmpty())
    <div class="text-center py-20">
        <div class="text-7xl mb-6">🛒</div>
        <h2 class="text-2xl font-bold text-gray-700 mb-3">{{ __('general.your_cart_is_empty') }}</h2>
        <p class="text-gray-500 mb-6">{{ __('general.no_plants_added') }}</p>
        <a href="{{ route('shop.index') }}" class="bg-primary-600 text-white px-8 py-3 rounded-xl font-semibold hover:bg-primary-400 transition">{{ __('general.start_shopping') }}</a>
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="{
        couponCode: '',
        couponMsg: '',
        applyCoupon() {
            fetch('/cart/coupon', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                body: JSON.stringify({code: this.couponCode})
            }).then(r => r.json()).then(d => { this.couponMsg = d.message; if(d.success) window.location.reload(); });
        }
    }">
        <!-- Cart Items -->
        <div class="lg:col-span-2 space-y-4">
            @foreach($cart->items as $item)
            <div class="bg-white rounded-2xl p-4 flex items-center gap-4 shadow-sm" x-data="{ qty: {{ $item->quantity }} }">
                <img src="{{ $item->product->primaryImage?->url ?? asset('images/placeholder.jpg') }}" alt="{{ $item->product->name }}" class="w-20 h-20 object-cover rounded-xl">
                <div class="flex-1">
                    <a href="{{ route('shop.show', $item->product->slug) }}" class="font-semibold text-gray-800 hover:text-primary-600 transition">{{ $item->product->name }}</a>
                    @if($item->variant)
                    <div class="text-xs text-gray-500">{{ $item->variant->name }}</div>
                    @endif
                    <div class="text-primary-600 font-bold mt-1">৳{{ number_format($item->unit_price, 0) }}</div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click.prevent="qty = Math.max(1, qty-1); fetch('/cart/item/{{ $item->id }}', {method:'PATCH', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({quantity:qty})}).then(() => window.location.reload())" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200">-</button>
                    <span x-text="qty" class="w-8 text-center font-semibold"></span>
                    <button type="button" @click.prevent="qty++; fetch('/cart/item/{{ $item->id }}', {method:'PATCH', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({quantity:qty})}).then(() => window.location.reload())" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200">+</button>
                </div>
                <div class="font-bold text-gray-800 min-w-[80px] text-right">৳{{ number_format($item->total, 0) }}</div>
                <button type="button" @click.prevent="fetch('/cart/item/{{ $item->id }}', {method:'DELETE', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(() => window.location.reload())"
                    class="text-red-400 hover:text-red-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
            @endforeach
        </div>

        <!-- Order Summary -->
        <div class="space-y-4">
            <!-- Coupon -->
            <div class="bg-white rounded-2xl p-5 shadow-sm">
                <h3 class="font-bold text-gray-800 mb-3">{{ __('general.coupon_code') }}</h3>
                <div class="flex gap-2">
                    <input x-model="couponCode" type="text" placeholder="{{ __('general.enter_code') }}" class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary-600">
                    <button @click="applyCoupon()" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-400 transition">{{ __('general.apply') }}</button>
                </div>
                <p x-show="couponMsg" x-text="couponMsg" class="text-sm mt-2 text-gray-600"></p>
            </div>

            <!-- Summary -->
            <div class="bg-white rounded-2xl p-5 shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">{{ __('general.order_summary') }}</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>{{ __('general.subtotal') }}</span>
                        <span>৳{{ number_format($cart->subtotal, 0) }}</span>
                    </div>
                    @if($cart->coupon)
                    <div class="flex justify-between text-green-600">
                        <span>{{ __('general.discount') }} ({{ $cart->coupon->code }})</span>
                        <span>-৳{{ number_format($cart->coupon->calculateDiscount($cart->subtotal), 0) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-gray-600">
                        <span>{{ __('general.shipping') }}</span>
                        <span class="text-primary-600">{{ __('general.calculated_at_checkout') }}</span>
                    </div>
                    <hr>
                    <div class="flex justify-between font-bold text-gray-800 text-base">
                        <span>{{ __('general.total') }}</span>
                        @php
                            $cartDiscount = $cart->coupon ? $cart->coupon->calculateDiscount($cart->subtotal) : 0;
                            $cartTotal = max(0, $cart->subtotal - $cartDiscount);
                        @endphp
                        <span>৳{{ number_format($cartTotal, 0) }}</span>
                    </div>
                </div>
                <a href="{{ route('checkout.index') }}" class="block text-center mt-5 bg-primary-600 text-white py-3 rounded-xl font-semibold hover:bg-primary-400 transition">
                    {{ __('general.proceed_to_checkout') }} →
                </a>
                <a href="{{ route('shop.index') }}" class="block text-center mt-3 text-sm text-gray-500 hover:text-primary-600 transition">← {{ __('general.continue_shopping') }}</a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
