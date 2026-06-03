@props(['product'])

<div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 group overflow-hidden"
    x-data="{ inWishlist: false, adding: false }">

    <!-- Image -->
    <div class="relative overflow-hidden">
        <a href="{{ route('shop.show', $product->slug) }}">
            <img src="{{ $product->primaryImage?->url ?? asset('images/placeholder.jpg') }}"
                alt="{{ $product->primaryImage?->alt_text ?? $product->name }}"
                class="w-full h-52 object-cover group-hover:scale-105 transition-transform duration-500 lazy">
        </a>

        <!-- Badges -->
        <div class="absolute top-2 left-2 flex flex-col gap-1">
            @if($product->is_new_arrival)
                <span class="bg-primary-600 text-white text-xs px-2 py-1 rounded-full">{{ __('general.new') }}</span>
            @endif
            @if($product->discount_percentage > 0)
                <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">-{{ $product->discount_percentage }}%</span>
            @endif
        </div>

        <!-- Wishlist Button -->
        @auth
        <button @click="
            adding = true;
            fetch('/wishlist/toggle', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                body: JSON.stringify({product_id: {{ $product->id }}})
            }).then(r => r.json()).then(data => {
                inWishlist = data.added;
                adding = false;
            });
        "
        class="absolute top-2 right-2 bg-white rounded-full p-2 shadow hover:bg-red-50 transition">
            <svg class="w-4 h-4" :class="inWishlist ? 'fill-red-500 stroke-red-500' : 'fill-none stroke-gray-400'" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>
        @endauth
    </div>

    <!-- Content -->
    <div class="p-4">
        @if($product->category)
            <span class="text-xs text-primary-400 font-medium">{{ $product->category->name }}</span>
        @endif

        <a href="{{ route('shop.show', $product->slug) }}">
            <h3 class="font-semibold text-gray-800 mt-1 hover:text-primary-600 transition line-clamp-2">{{ $product->name }}</h3>
        </a>

        <!-- Rating -->
        <div class="flex items-center gap-1 mt-1">
            @for($i = 1; $i <= 5; $i++)
                <svg class="w-3 h-3 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            @endfor
        </div>

        <!-- Price -->
        <div class="flex items-center justify-between mt-3">
            <div>
                <span class="text-primary-600 font-bold text-lg">৳{{ number_format($product->price, 0) }}</span>
                @if($product->compare_price)
                    <span class="text-gray-400 text-sm line-through ml-1">৳{{ number_format($product->compare_price, 0) }}</span>
                @endif
            </div>

            <!-- Add to Cart -->
            @if($product->isInStock())
            <button @click="
                adding = true;
                fetch('/cart/add', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    body: JSON.stringify({product_id: {{ $product->id }}, quantity: 1})
                }).then(r => r.json()).then(data => {
                    adding = false;
                    document.querySelectorAll('.cart-count').forEach(el => el.textContent = data.cart_count);
                    $dispatch('toast', { message: '{{ __('general.added_to_cart') }}' });
                });
            "
            :disabled="adding"
            class="bg-primary-600 text-white p-2 rounded-lg hover:bg-primary-400 transition disabled:opacity-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </button>
            @else
            <span class="text-xs text-red-500 font-medium">{{ __('general.out_of_stock') }}</span>
            @endif
        </div>
    </div>
</div>
