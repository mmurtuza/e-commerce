@extends('layouts.app')

@section('title', config('app.name') . ' - ' . __('general.online_plant_paradise'))
@section('meta_description', __('general.meta_description_default'))

@section('content')

<x-hero-slider :banners="$banners" />

<!-- Features Strip -->
<section class="max-w-7xl mx-auto px-4 mt-8">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 shadow-2xl rounded-3xl p-6 flex flex-col items-center justify-center gap-3 hover:-translate-y-2 hover:shadow-[0_0_30px_rgba(34,211,238,0.3)] transition-all duration-300">
            <span class="text-4xl filter drop-shadow-[0_0_10px_rgba(255,255,255,0.8)]">🚚</span>
            <span class="font-medium text-white tracking-wide text-sm">{{ __('general.free_delivery_above') }}</span>
        </div>
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 shadow-2xl rounded-3xl p-6 flex flex-col items-center justify-center gap-3 hover:-translate-y-2 hover:shadow-[0_0_30px_rgba(192,38,211,0.3)] transition-all duration-300">
            <span class="text-4xl filter drop-shadow-[0_0_10px_rgba(255,255,255,0.8)]">💻</span>
            <span class="font-medium text-white tracking-wide text-sm">{{ __('general.healthy_plants') }}</span>
        </div>
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 shadow-2xl rounded-3xl p-6 flex flex-col items-center justify-center gap-3 hover:-translate-y-2 hover:shadow-[0_0_30px_rgba(52,211,153,0.3)] transition-all duration-300">
            <span class="text-4xl filter drop-shadow-[0_0_10px_rgba(255,255,255,0.8)]">🔄</span>
            <span class="font-medium text-white tracking-wide text-sm">{{ __('general.easy_returns') }}</span>
        </div>
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 shadow-2xl rounded-3xl p-6 flex flex-col items-center justify-center gap-3 hover:-translate-y-2 hover:shadow-[0_0_30px_rgba(59,130,246,0.3)] transition-all duration-300">
            <span class="text-4xl filter drop-shadow-[0_0_10px_rgba(255,255,255,0.8)]">💬</span>
            <span class="font-medium text-white tracking-wide text-sm">{{ __('general.support') }}</span>
        </div>
    </div>
</section>

<!-- Liquid Categories -->
<section class="py-20 max-w-7xl mx-auto px-4 relative z-10">
    <div class="text-center mb-14">
        <h2 class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-cyan-300 to-purple-400 drop-shadow-md" style="font-family:'Inter',sans-serif">{{ __('general.browse_categories') }}</h2>
        <p class="text-slate-300 mt-4 text-xl font-light">{{ __('general.find_exactly_what_you_need') }}</p>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-6">
        @foreach($categories as $category)
        <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
            class="bg-white/5 backdrop-blur-2xl border border-white/10 rounded-[2rem] p-5 text-center shadow-[0_8px_30px_rgb(0,0,0,0.2)] hover:shadow-[0_0_40px_rgba(34,211,238,0.4)] hover:-translate-y-3 hover:bg-white/10 transition-all duration-300 group">
            @if($category->image)
                <div class="w-20 h-20 mx-auto mb-4 rounded-full overflow-hidden bg-white/20 backdrop-blur-md shadow-inner p-3 group-hover:rotate-12 transition-transform duration-500">
                    <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-contain filter drop-shadow-md">
                </div>
            @else
                <div class="w-20 h-20 bg-gradient-to-br from-cyan-400/20 to-purple-500/20 rounded-full mx-auto mb-4 flex items-center justify-center text-4xl shadow-[inset_0_0_20px_rgba(255,255,255,0.2)] group-hover:scale-110 group-hover:rotate-12 transition-all duration-500">
                    {{ $category->icon ?? '🔌' }}
                </div>
            @endif
            <span class="text-sm font-bold text-white group-hover:text-cyan-300 transition-colors">{{ $category->name }}</span>
            @if(isset($category->products_count))
                <span class="block text-xs text-slate-400 mt-2 font-medium bg-white/10 rounded-full py-1 px-3 w-max mx-auto shadow-inner">{{ $category->products_count }} {{ __('general.items') }}</span>
            @endif
        </a>
        @endforeach
    </div>
</section>

<!-- Featured Products -->
<section class="relative z-10 py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-cyan-300 to-purple-400 drop-shadow-md">{{ __('general.featured_plants') }}</h2>
                <p class="text-slate-300 mt-2 text-lg font-light">{{ __('general.handpicked_favorites') }}</p>
            </div>
            <a href="{{ route('shop.index') }}" class="text-cyan-400 font-bold hover:text-cyan-300 transition-colors drop-shadow-[0_0_10px_rgba(34,211,238,0.5)]">{{ __('general.view_all') }} →</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
            @foreach($featured as $product)
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.3)] hover:shadow-[0_0_40px_rgba(34,211,238,0.3)] transition-all duration-300">
                    <x-product-card :product="$product" />
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- New Arrivals -->
<section class="py-16 max-w-7xl mx-auto px-4 relative z-10">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h2 class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-fuchsia-400 to-purple-400 drop-shadow-md">{{ __('general.new_arrivals') }}</h2>
            <p class="text-slate-300 mt-2 text-lg font-light">{{ __('general.new_arrivals_subtitle') }}</p>
        </div>
        <a href="{{ route('shop.index') }}" class="text-fuchsia-400 font-bold hover:text-fuchsia-300 transition-colors drop-shadow-[0_0_10px_rgba(232,121,249,0.5)]">{{ __('general.view_all') }} →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
        @foreach($newArrivals as $product)
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.3)] hover:shadow-[0_0_40px_rgba(232,121,249,0.3)] transition-all duration-300">
                <x-product-card :product="$product" />
            </div>
        @endforeach
    </div>
</section>

<!-- Customer Reviews -->
@if($reviews->isNotEmpty())
<section class="py-16 text-white relative z-10">
    <div class="absolute inset-0 bg-white/5 backdrop-blur-3xl border-y border-white/10 -z-10"></div>
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-300 drop-shadow-md">{{ __('general.what_our_customers_say') }}</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($reviews as $review)
            <div class="bg-white/10 backdrop-blur-2xl border border-white/20 shadow-[0_8px_30px_rgb(0,0,0,0.3)] rounded-3xl p-8 hover:-translate-y-2 hover:shadow-[0_0_30px_rgba(52,211,153,0.3)] transition-all duration-300">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center font-black text-white text-xl shadow-[0_0_15px_rgba(52,211,153,0.5)]">
                        {{ strtoupper(substr($review->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-bold text-lg text-white">{{ $review->user->name }}</div>
                        <div class="text-xs text-emerald-300">{{ $review->product?->name }}</div>
                    </div>
                </div>
                <div class="flex gap-1 mb-4">
                    @for($i = 1; $i <= 5; $i++)
                    <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-300 drop-shadow-[0_0_5px_rgba(253,224,71,0.5)]' : 'text-white/20' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    @endfor
                </div>
                <p class="text-sm text-slate-200 leading-relaxed font-light">{{ $review->comment }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Latest Blog Posts -->
@if($blogs->isNotEmpty())
<section class="py-16 max-w-7xl mx-auto px-4 relative z-10">
    <div class="text-center mb-10">
        <h2 class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-cyan-300 drop-shadow-md">{{ __('general.plant_care_tips') }}</h2>
        <p class="text-slate-300 mt-2 text-lg font-light">{{ __('general.learn_to_grow_nurture') }}</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach($blogs as $blog)
        <article class="bg-white/5 backdrop-blur-2xl border border-white/10 rounded-3xl overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.3)] hover:shadow-[0_0_30px_rgba(59,130,246,0.3)] transition-all duration-300 group">
            @if($blog->featured_image)
            <div class="relative overflow-hidden h-56">
                <img src="{{ Storage::url($blog->featured_image) }}" alt="{{ $blog->title }}" class="w-full h-full object-cover group-hover:scale-110 group-hover:rotate-1 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>
            </div>
            @else
            <div class="w-full h-56 bg-gradient-to-br from-blue-500/20 to-cyan-400/20 flex items-center justify-center text-5xl">💻</div>
            @endif
            <div class="p-6 relative">
                <span class="absolute -top-6 left-6 bg-cyan-500/20 backdrop-blur-md border border-cyan-400/30 text-cyan-300 px-4 py-1 rounded-full text-xs font-bold shadow-lg">{{ $blog->category?->name }}</span>
                <h3 class="font-bold text-white text-xl mt-3 group-hover:text-cyan-300 transition-colors">{{ $blog->title }}</h3>
                <p class="text-slate-300 text-sm mt-3 font-light leading-relaxed line-clamp-2">{{ $blog->excerpt }}</p>
                <a href="{{ route('blog.show', $blog->slug) }}" class="inline-flex items-center gap-2 mt-5 text-cyan-400 text-sm font-bold hover:text-cyan-300 transition-colors">
                    {{ __('general.read_more') }} <span class="group-hover:translate-x-1 transition-transform">→</span>
                </a>
            </div>
        </article>
        @endforeach
    </div>
</section>
@endif

<!-- Newsletter CTA -->
<section class="py-24 text-center relative z-10" x-data="{ email: '', msg: '' }">
    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-gradient-to-br from-cyan-500/20 via-blue-500/20 to-purple-500/20 backdrop-blur-3xl border border-white/20 rounded-[3rem] p-12 shadow-[0_0_50px_rgba(34,211,238,0.2)]">
            <h2 class="text-4xl font-black text-white mb-4 drop-shadow-md">{{ __('general.get_tips_in_inbox') }}</h2>
            <p class="text-slate-300 mb-8 text-lg font-light">{{ __('general.subscribe_for_offers') }}</p>
            <div class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                <input x-model="email" type="email" placeholder="{{ __('Enter your email') }}"
                    class="flex-1 bg-white/10 backdrop-blur-md border border-white/20 rounded-full px-6 py-4 text-white placeholder-slate-400 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30 transition-all shadow-inner">
                <button @click="
                    fetch('/newsletter/subscribe', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        body: JSON.stringify({email})
                    }).then(r => r.json()).then(d => { msg = d.message; email = ''; });
                " class="bg-cyan-500 hover:bg-cyan-400 text-slate-900 px-8 py-4 rounded-full font-black shadow-[0_0_20px_rgba(34,211,238,0.4)] hover:shadow-[0_0_30px_rgba(34,211,238,0.6)] hover:-translate-y-1 transition-all duration-300">
                    {{ __('general.subscribe') }}
                </button>
            </div>
            <p x-show="msg" x-text="msg" class="mt-4 text-cyan-300 text-sm font-medium"></p>
        </div>
    </div>
</section>

@endsection
