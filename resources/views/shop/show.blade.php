@extends('layouts.app')

@section('title', $product->getTranslation('meta_title') ?: ($product->name . ' | ' . \App\Models\Setting::get('site_name', 'Dinajpur IT Park')))
@section('meta_description', $product->getTranslation('meta_description') ?: $product->short_description)
@section('og_title', $product->name)
@section('og_description', $product->short_description)
@section('og_type', 'product')
@section('og_image', $product->primaryImage ? asset($product->primaryImage->url) : null)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <x-breadcrumb :items="[
        ['name' => __('general.shop'), 'url' => route('shop.index')],
        ['name' => $product->category?->name, 'url' => route('shop.index', ['category' => $product->category?->slug])],
        ['name' => $product->name]
    ]" />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-16" x-data="{
        activeImage: '{{ $product->primaryImage?->url ?? asset('images/placeholder.jpg') }}',
        quantity: 1,
        selectedVariant: null,
        adding: false,
        addToCart() {
            this.adding = true;
            fetch('/cart/add', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                body: JSON.stringify({product_id: {{ $product->id }}, quantity: this.quantity, variant_id: this.selectedVariant})
            }).then(r => r.json()).then(d => {
                this.adding = false;
                document.querySelectorAll('.cart-count').forEach(el => el.textContent = d.cart_count);
                this.$dispatch('toast', { message: '{{ __('general.added_to_cart') }}' });
            });
        }
    }">

        <!-- Image Gallery -->
        <div>
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm mb-3">
                <img :src="activeImage" alt="{{ $product->name }}" class="w-full h-96 object-contain p-4">
            </div>
            @if($product->images->count() > 1)
            <div class="flex gap-2 overflow-x-auto pb-2">
                @foreach($product->images as $image)
                <button @click="activeImage = '{{ $image->url }}'" class="shrink-0">
                    <img src="{{ $image->url }}" alt="{{ $image->alt_text }}" class="w-16 h-16 object-cover rounded-lg border-2 hover:border-primary-600 transition">
                </button>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Product Info -->
        <div>
            @if($product->category)
            <span class="text-sm text-primary-400 font-medium">{{ $product->category->name }}</span>
            @endif
            <h1 class="text-3xl font-bold text-gray-800 mt-2">{{ $product->name }}</h1>

            <!-- Rating -->
            <div class="flex items-center gap-2 mt-2">
                <div class="flex gap-1">
                    @for($i = 1; $i <= 5; $i++)
                    <svg class="w-4 h-4 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    @endfor
                </div>
                <span class="text-sm text-gray-500">({{ $product->reviews->where('is_approved', true)->count() }} {{ __('general.reviews') }})</span>
            </div>

            <!-- Price -->
            <div class="mt-4 flex items-baseline gap-3">
                <span class="text-3xl font-bold text-primary-600">৳{{ number_format($product->price, 0) }}</span>
                @if($product->compare_price)
                <span class="text-xl text-gray-400 line-through">৳{{ number_format($product->compare_price, 0) }}</span>
                <span class="bg-red-100 text-red-600 text-sm px-2 py-0.5 rounded-full">-{{ $product->discount_percentage }}%</span>
                @endif
            </div>

            <p class="text-gray-600 mt-4 leading-relaxed">{{ $product->short_description }}</p>

            <!-- Tech Info Badges -->
            <div class="flex flex-wrap gap-2 mt-5">
                @if($product->plant_type)
                <span class="bg-green-50 text-green-700 text-xs px-3 py-1.5 rounded-full border border-green-100">
                    🔌 {{ $product->plant_type->label() }}
                </span>
                @endif
                @if($product->sunlight)
                <span class="bg-yellow-50 text-yellow-700 text-xs px-3 py-1.5 rounded-full border border-yellow-100">
                    ☀️ {{ $product->sunlight->label() }}
                </span>
                @endif
                @if($product->watering)
                <span class="bg-blue-50 text-blue-700 text-xs px-3 py-1.5 rounded-full border border-blue-100">
                    💧 {{ $product->watering->label() }}
                </span>
                @endif
                @if($product->difficulty)
                <span class="bg-purple-50 text-purple-700 text-xs px-3 py-1.5 rounded-full border border-purple-100">
                    📊 {{ $product->difficulty->label() }}
                </span>
                @endif
            </div>

            <!-- Variants -->
            @if($product->variants->isNotEmpty())
            <div class="mt-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('general.select_variant') }}</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($product->variants->where('is_active', true) as $variant)
                    <button @click="selectedVariant = {{ $variant->id }}"
                        :class="selectedVariant === {{ $variant->id }} ? 'border-primary-600 bg-primary-600 text-white' : 'border-gray-200 text-gray-700'"
                        class="px-4 py-2 border rounded-lg text-sm font-medium transition">
                        {{ $variant->name }}
                        @if($variant->price_modifier != 0)
                        ({{ $variant->price_modifier > 0 ? '+' : '' }}৳{{ number_format($variant->price_modifier, 0) }})
                        @endif
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Quantity + Add to Cart -->
            <div class="flex items-center gap-4 mt-6">
                <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="quantity = Math.max(1, quantity - 1)" class="px-3 py-2 bg-gray-50 hover:bg-gray-100 transition text-lg font-medium">-</button>
                    <span x-text="quantity" class="px-4 py-2 font-semibold min-w-[3rem] text-center"></span>
                    <button @click="quantity++" class="px-3 py-2 bg-gray-50 hover:bg-gray-100 transition text-lg font-medium">+</button>
                </div>

                @if($product->isInStock())
                <button @click="addToCart()" :disabled="adding"
                    class="flex-1 bg-primary-600 text-white py-3 rounded-xl font-semibold hover:bg-primary-400 transition disabled:opacity-60">
                    <span x-text="adding ? '{{ __('general.adding') }}' : '{{ __('general.add_to_cart') }}'"></span>
                </button>
                @else
                <span class="flex-1 text-center bg-gray-100 text-gray-500 py-3 rounded-xl font-semibold">{{ __('general.out_of_stock') }}</span>
                @endif
            </div>

            <!-- Stock indicator -->
            <div class="mt-2 text-sm">
                @if($product->stock_quantity <= 5 && $product->stock_quantity > 0)
                <span class="text-orange-500">⚠️ {{ __('general.only_left_in_stock', ['count' => $product->stock_quantity]) }}</span>
                @elseif($product->isInStock())
                <span class="text-green-600">✓ {{ __('general.in_stock') }}</span>
                @else
                <span class="text-red-500">✗ {{ __('general.out_of_stock') }}</span>
                @endif
            </div>

            <!-- WhatsApp Order -->
            <a href="https://wa.me/{{ config('gardenngrow.whatsapp_number') }}?text=Hi, I want to order: {{ urlencode($product->name) }}" target="_blank"
                class="mt-4 flex items-center justify-center gap-2 border-2 border-green-500 text-green-600 py-3 rounded-xl font-medium hover:bg-green-50 transition">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                {{ __('general.order_via_whatsapp') }}
            </a>
        </div>
    </div>

    <!-- Tabs: Description / Care Tips / Reviews -->
    <div x-data="{ tab: 'description' }" class="mb-16">
        <div class="flex border-b mb-6">
            @foreach(['description' => __('general.description'), 'care' => __('general.care_tips'), 'reviews' => __('general.reviews')] as $key => $label)
            <button @click="tab = '{{ $key }}'"
                :class="tab === '{{ $key }}' ? 'border-b-2 border-primary-600 text-primary-600 font-semibold' : 'text-gray-500'"
                class="px-6 py-3 text-sm transition">{{ $label }}</button>
            @endforeach
        </div>

        <div x-show="tab === 'description'" class="prose max-w-none text-gray-700">
            {!! $product->description !!}
        </div>

        <div x-show="tab === 'care'">
            @if($product->getTranslation('care_instructions'))
            <div class="bg-green-50 border border-green-100 rounded-2xl p-6">
                <h3 class="font-bold text-gray-800 mb-4">🔌 {{ __('general.plant_care_instructions') }}</h3>
                <div class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $product->getTranslation('care_instructions') }}</div>
            </div>
            @else
            <p class="text-gray-500">{{ __('general.no_care_instructions') }}</p>
            @endif
        </div>

        <div x-show="tab === 'reviews'">
            <div class="space-y-4 mb-8">
                @forelse($product->reviews->where('is_approved', true) as $review)
                <div class="bg-white rounded-xl p-5 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-primary-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr($review->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-gray-800">{{ $review->user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                    </div>
                    @if($review->title)
                    <h4 class="font-semibold mt-3">{{ $review->title }}</h4>
                    @endif
                    <p class="text-gray-600 text-sm mt-1">{{ $review->comment }}</p>
                </div>
                @empty
                <p class="text-gray-500">{{ __('general.no_reviews_yet') }}</p>
                @endforelse
            </div>

            <!-- Submit Review Form -->
            @auth
            <div class="bg-[var(--theme-bg)] rounded-2xl p-6">
                <h3 class="font-bold text-gray-800 mb-4">{{ __('general.write_a_review') }}</h3>
                <form method="POST" action="{{ route('reviews.store') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="mb-4" x-data="{ rating: 0 }">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('general.rating') }}</label>
                        <div class="flex gap-1">
                            @for($i = 1; $i <= 5; $i++)
                            <button type="button" @click="rating = {{ $i }}" class="w-8 h-8 text-2xl">
                                <svg :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-200'" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </button>
                            @endfor
                            <input type="hidden" name="rating" :value="rating">
                        </div>
                    </div>
                    <div class="mb-4">
                        <textarea name="comment" rows="3" placeholder="{{ __('general.share_your_experience') }}"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm outline-none focus:border-primary-600"></textarea>
                    </div>
                    <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-primary-400 transition">
                        {{ __('general.submit_review') }}
                    </button>
                </form>
            </div>
            @else
            <p class="text-gray-500 text-sm"><a href="{{ route('login') }}" class="text-primary-600">{{ __('general.login') }}</a> {{ __('general.to_write_review_login_suffix') }}</p>
            @endauth
        </div>
    </div>

    <!-- Related Products -->
    @if($related->isNotEmpty())
    <div>
        <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('general.related_products') }}</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
            @foreach($related as $relatedProduct)
                <x-product-card :product="$relatedProduct" />
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
