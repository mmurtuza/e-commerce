@extends('layouts.app')

@section('title', config('app.name') . ' - ' . __('general.online_plant_paradise'))
@section('meta_description', __('general.meta_description_default'))

@section('content')
<div class="max-w-7xl mx-auto px-4 mt-6">
    <div class="bg-white border border-gray-200 shadow-sm">
        <x-hero-slider :banners="$banners" />
    </div>
</div>

<!-- Features Strip -->
<section class="max-w-7xl mx-auto px-4 mt-4">
    <div class="bg-white border border-gray-200">
        <div class="py-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-center text-sm divide-x divide-gray-100">
            <div class="flex flex-col items-center justify-center text-gray-700">
                <span class="text-3xl mb-1 text-primary-600">🚚</span>
                <span class="font-bold uppercase tracking-wider text-xs">{{ __('general.free_delivery_above') }}</span>
            </div>
            <div class="flex flex-col items-center justify-center text-gray-700">
                <span class="text-3xl mb-1 text-primary-600">💻</span>
                <span class="font-bold uppercase tracking-wider text-xs">{{ __('general.healthy_plants') }}</span>
            </div>
            <div class="flex flex-col items-center justify-center text-gray-700">
                <span class="text-3xl mb-1 text-primary-600">🔄</span>
                <span class="font-bold uppercase tracking-wider text-xs">{{ __('general.easy_returns') }}</span>
            </div>
            <div class="flex flex-col items-center justify-center text-gray-700">
                <span class="text-3xl mb-1 text-primary-600">💬</span>
                <span class="font-bold uppercase tracking-wider text-xs">{{ __('general.support') }}</span>
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="py-16 max-w-7xl mx-auto px-4">
    <div class="text-center mb-10">
        <h2 class="text-3xl font-bold text-gray-800">{{ __('general.browse_categories') }}</h2>
        <p class="text-gray-500 mt-2">{{ __('general.find_exactly_what_you_need') }}</p>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-4">
        @foreach($categories as $category)
        <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
            class="bg-white rounded-2xl p-4 text-center hover:shadow-md hover:-translate-y-1 transition-all duration-300 group">
            @if($category->image)
                <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="w-16 h-16 object-cover rounded-xl mx-auto mb-3">
            @else
                <div class="w-16 h-16 bg-[var(--theme-bg)] rounded-xl mx-auto mb-3 flex items-center justify-center text-3xl">
                    {{ $category->icon ?? '🔌' }}
                </div>
            @endif
            <span class="text-sm font-medium text-gray-700 group-hover:text-primary-600 transition">{{ $category->name }}</span>
            @if(isset($category->products_count))
                <span class="block text-xs text-gray-400">{{ $category->products_count }} {{ __('general.items') }}</span>
            @endif
        </a>
        @endforeach
    </div>
</section>

<!-- Featured Products -->
<section class="bg-[var(--theme-bg)] py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">{{ __('general.featured_plants') }}</h2>
                <p class="text-gray-500 mt-1">{{ __('general.handpicked_favorites') }}</p>
            </div>
            <a href="{{ route('shop.index') }}" class="text-primary-600 font-medium hover:text-primary-400 transition">{{ __('general.view_all') }} →</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5">
            @foreach($featured as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
</section>

<!-- New Arrivals -->
<section class="py-16 max-w-7xl mx-auto px-4">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">{{ __('general.new_arrivals') }}</h2>
            <p class="text-gray-500 mt-1">{{ __('general.new_arrivals_subtitle') }}</p>
        </div>
        <a href="{{ route('shop.index') }}" class="text-primary-600 font-medium hover:text-primary-400 transition">{{ __('general.view_all') }} →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5">
        @foreach($newArrivals as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>
</section>

<!-- Customer Reviews -->
@if($reviews->isNotEmpty())
<section class="bg-primary-600 py-16 text-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold">{{ __('general.what_our_customers_say') }}</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($reviews as $review)
            <div class="bg-white/10 backdrop-blur rounded-2xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-primary-400 rounded-full flex items-center justify-center font-bold text-white">
                        {{ strtoupper(substr($review->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-semibold">{{ $review->user->name }}</div>
                        <div class="text-xs text-primary-300">{{ $review->product?->name }}</div>
                    </div>
                </div>
                <div class="flex gap-1 mb-3">
                    @for($i = 1; $i <= 5; $i++)
                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-white/30' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    @endfor
                </div>
                <p class="text-sm text-gray-100 leading-relaxed">{{ $review->comment }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Latest Blog Posts -->
@if($blogs->isNotEmpty())
<section class="py-16 max-w-7xl mx-auto px-4">
    <div class="text-center mb-10">
        <h2 class="text-3xl font-bold text-gray-800">{{ __('general.plant_care_tips') }}</h2>
        <p class="text-gray-500 mt-2">{{ __('general.learn_to_grow_nurture') }}</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($blogs as $blog)
        <article class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition group">
            @if($blog->featured_image)
            <img src="{{ Storage::url($blog->featured_image) }}" alt="{{ $blog->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500">
            @else
            <div class="w-full h-48 bg-gradient-to-br from-primary-300 to-primary-400 flex items-center justify-center text-5xl">💻</div>
            @endif
            <div class="p-5">
                <span class="text-xs text-primary-400 font-medium">{{ $blog->category?->name }}</span>
                <h3 class="font-bold text-gray-800 mt-1 group-hover:text-primary-600 transition">{{ $blog->title }}</h3>
                <p class="text-gray-500 text-sm mt-2 line-clamp-2">{{ $blog->excerpt }}</p>
                <a href="{{ route('blog.show', $blog->slug) }}" class="inline-block mt-4 text-primary-600 text-sm font-medium hover:text-primary-400 transition">
                    {{ __('general.read_more') }} →
                </a>
            </div>
        </article>
        @endforeach
    </div>
</section>
@endif

<!-- Newsletter CTA -->
<section class="bg-gradient-to-r from-primary-600 to-primary-400 py-16 text-white text-center" x-data="{ email: '', msg: '' }">
    <div class="max-w-xl mx-auto px-4">
        <h2 class="text-3xl font-bold mb-3">{{ __('general.get_tips_in_inbox') }}</h2>
        <p class="text-primary-300 mb-6">{{ __('general.subscribe_for_offers') }}</p>
        <div class="flex gap-2 max-w-md mx-auto">
            <input x-model="email" type="email" placeholder="{{ __('Enter your email') }}"
                class="flex-1 rounded-l-lg px-4 py-3 text-gray-800 outline-none">
            <button @click="
                fetch('/newsletter/subscribe', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    body: JSON.stringify({email})
                }).then(r => r.json()).then(d => { msg = d.message; email = ''; });
            " class="bg-[#8B6914] text-white px-6 py-3 rounded-r-lg font-semibold hover:bg-primary-300 hover:text-primary-600 transition">
                {{ __('general.subscribe') }}
            </button>
        </div>
        <p x-show="msg" x-text="msg" class="mt-3 text-primary-300 text-sm"></p>
    </div>
</section>

@endsection
