@props(['banners'])

@php
    $heroStyle = \App\Models\Setting::get('hero_style', 'carousel');
    $slidesCount = $banners->count() ?: 1;
@endphp

@if($heroStyle === 'split')
    <!-- Style 2: Modern Split Screen Layout -->
    <section class="relative overflow-hidden bg-gradient-to-br from-primary-50 via-white to-primary-100 py-8 md:py-16" 
             x-data="{ current: 0, slides: {{ $slidesCount }} }" 
             x-init="setInterval(() => current = (current + 1) % slides, 6000)">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative min-h-[500px] md:min-h-[600px] flex items-center">
            @forelse($banners as $index => $banner)
                <div x-show="current === {{ $index }}" 
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-300 absolute inset-0"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center w-full">
                    
                    <!-- Left side: Text Content -->
                    <div class="space-y-6 text-left"
                         x-show="current === {{ $index }}"
                         x-transition:enter="transition ease-out duration-700 delay-150"
                         x-transition:enter-start="opacity-0 translate-x-[-30px]"
                         x-transition:enter-end="opacity-100 translate-x-0">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-primary-100 text-primary-800 uppercase tracking-wider">
                            ✨ {{ __('general.featured') ?? 'Featured' }}
                        </span>
                        <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-gray-900 leading-tight" style="font-family: 'Playfair Display', serif">
                            {{ $banner->translate()?->title ?? 'Welcome to Dinajpur IT Park' }}
                        </h1>
                        <p class="text-lg md:text-xl text-gray-600 leading-relaxed">
                            {{ $banner->translate()?->subtitle ?? 'Your Ultimate Computer Accessories Store in Bangladesh' }}
                        </p>
                        <div>
                            <a href="{{ $banner->link ?? route('shop.index') }}"
                               class="inline-flex items-center justify-center bg-primary-600 text-white px-8 py-3.5 rounded-xl text-lg font-bold shadow-lg shadow-primary-600/20 hover:bg-primary-700 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                                {{ $banner->translate()?->button_text ?? __('general.shop_now') }}
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Right side: Image Card Showcase -->
                    <div class="flex justify-center"
                         x-show="current === {{ $index }}"
                         x-transition:enter="transition ease-out duration-700 delay-300"
                         x-transition:enter-start="opacity-0 translate-x-[30px]"
                         x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="relative w-full max-w-md aspect-square md:aspect-[4/3] rounded-3xl overflow-hidden shadow-2xl border-4 border-white bg-white group">
                            <img src="{{ Storage::url($banner->image) }}" 
                                 alt="{{ $banner->translate()?->title }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Fallback split screen layout -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center w-full">
                    <div class="space-y-6 text-left">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-primary-100 text-primary-800 uppercase tracking-wider">
                            ✨ Welcome
                        </span>
                        <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-gray-900 leading-tight" style="font-family: 'Playfair Display', serif">
                            💻 Dinajpur IT Park
                        </h1>
                        <p class="text-lg md:text-xl text-gray-600 leading-relaxed">
                            {{ __('general.premier_store') }}
                        </p>
                        <div>
                            <a href="{{ route('shop.index') }}"
                               class="inline-flex items-center justify-center bg-primary-600 text-white px-8 py-3.5 rounded-xl text-lg font-bold shadow-lg shadow-primary-600/20 hover:bg-primary-700 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                                {{ __('general.explore_plants') }}
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                    <div class="flex justify-center">
                        <div class="relative w-full max-w-md aspect-square md:aspect-[4/3] rounded-3xl overflow-hidden shadow-2xl border-4 border-white bg-primary-100 flex items-center justify-center text-7xl">
                            💻
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Slider dots -->
        @if($banners->count() > 1)
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2.5 z-10">
            @foreach($banners as $index => $banner)
            <button @click="current = {{ $index }}" class="w-3 h-3 rounded-full transition-all duration-300" :class="current === {{ $index }} ? 'bg-primary-600 scale-125' : 'bg-primary-200 hover:bg-primary-300'"></button>
            @endforeach
        </div>
        @endif
    </section>

@elseif($heroStyle === 'minimal')
    <!-- Style 3: Centered Minimalist Layout -->
    <section class="relative overflow-hidden bg-white border-b border-gray-100 py-12 md:py-20"
             x-data="{ current: 0, slides: {{ $slidesCount }} }" 
             x-init="setInterval(() => current = (current + 1) % slides, 6000)">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 relative flex flex-col items-center min-h-[550px] justify-between">
            @forelse($banners as $index => $banner)
                <div x-show="current === {{ $index }}"
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-300 absolute inset-x-0 top-0"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="flex flex-col items-center text-center w-full">
                    
                    <!-- Text Section -->
                    <div class="space-y-4 max-w-2xl"
                         x-show="current === {{ $index }}"
                         x-transition:enter="transition ease-out duration-500 delay-100"
                         x-transition:enter-start="opacity-0 translate-y-[-10px]"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <span class="text-xs uppercase tracking-widest font-bold text-primary-600 block">
                            {{ $banner->translate()?->button_text ?? __('general.shop_now') }}
                        </span>
                        <h1 class="text-3xl md:text-5xl font-light text-gray-900 tracking-wide" style="font-family: 'Playfair Display', serif">
                            {{ $banner->translate()?->title ?? 'Welcome to Dinajpur IT Park' }}
                        </h1>
                        <p class="text-md md:text-lg text-gray-500 font-light max-w-xl mx-auto">
                            {{ $banner->translate()?->subtitle ?? 'Your Ultimate Computer Accessories Store in Bangladesh' }}
                        </p>
                        <div class="pt-2">
                            <a href="{{ $banner->link ?? route('shop.index') }}"
                               class="inline-flex items-center gap-1 text-primary-600 font-semibold border-b-2 border-primary-600 pb-0.5 hover:text-primary-500 hover:border-primary-500 transition-all duration-300">
                                {{ __('general.shop_now') }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Framed Image Showcase -->
                    <div class="w-full max-w-4xl aspect-[21/9] rounded-2xl overflow-hidden shadow-lg border border-gray-100 mt-8 group"
                         x-show="current === {{ $index }}"
                         x-transition:enter="transition ease-out duration-700 delay-300"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100">
                        <img src="{{ Storage::url($banner->image) }}" 
                             alt="{{ $banner->translate()?->title }}" 
                             class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-700">
                    </div>
                </div>
            @empty
                <!-- Fallback minimal layout -->
                <div class="flex flex-col items-center text-center w-full">
                    <div class="space-y-4 max-w-2xl">
                        <span class="text-xs uppercase tracking-widest font-bold text-primary-600 block">Premium</span>
                        <h1 class="text-3xl md:text-5xl font-light text-gray-900 tracking-wide" style="font-family: 'Playfair Display', serif">
                            💻 Dinajpur IT Park
                        </h1>
                        <p class="text-md md:text-lg text-gray-500 font-light max-w-xl mx-auto">
                            {{ __('general.premier_store') }}
                        </p>
                        <div class="pt-2">
                            <a href="{{ route('shop.index') }}"
                               class="inline-flex items-center gap-1 text-primary-600 font-semibold border-b-2 border-primary-600 pb-0.5 hover:text-primary-500 hover:border-primary-500 transition-all duration-300">
                                {{ __('general.explore_plants') }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                    <div class="w-full max-w-4xl aspect-[21/9] rounded-2xl bg-gray-50 border border-gray-100 mt-8 flex items-center justify-center text-5xl">
                        💻
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Slider dots -->
        @if($banners->count() > 1)
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
            @foreach($banners as $index => $banner)
            <button @click="current = {{ $index }}" class="w-1.5 h-1.5 rounded-full transition-all duration-300" :class="current === {{ $index }} ? 'bg-primary-600 w-4' : 'bg-gray-300 hover:bg-gray-400'"></button>
            @endforeach
        </div>
        @endif
    </section>

@else
    <!-- Style 1: Standard Background Image Carousel (Default) -->
    <section class="relative overflow-hidden bg-primary-600" 
             x-data="{ current: 0, slides: {{ $slidesCount }} }" 
             x-init="setInterval(() => current = (current + 1) % slides, 5000)">
        @forelse($banners as $index => $banner)
        <div x-show="current === {{ $index }}" 
             x-transition:enter="transition ease-out duration-700"
             x-transition:enter-start="opacity-0 scale-98"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-300 absolute inset-0"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-98"
             class="relative min-h-[500px] md:min-h-[600px] flex items-center w-full" 
             style="background-image: url('{{ Storage::url($banner->image) }}'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-black/40"></div>
            <div class="relative max-w-7xl mx-auto px-4 text-white w-full">
                <div class="max-w-2xl">
                    <h1 class="text-4xl md:text-6xl font-bold mb-4 leading-tight" style="font-family: 'Playfair Display', serif">
                        {{ $banner->translate()?->title ?? 'Welcome to Dinajpur IT Park' }}
                    </h1>
                    <p class="text-xl md:text-2xl mb-8 text-gray-200 leading-relaxed">
                        {{ $banner->translate()?->subtitle ?? 'Your Ultimate Computer Accessories Store in Bangladesh' }}
                    </p>
                    <a href="{{ $banner->link ?? route('shop.index') }}"
                        class="inline-block bg-primary-500 text-white px-8 py-3.5 rounded-xl text-lg font-bold shadow-lg shadow-black/20 hover:bg-white hover:text-primary-700 hover:-translate-y-0.5 transition-all duration-300">
                        {{ $banner->translate()?->button_text ?? __('general.shop_now') }}
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="min-h-[500px] flex items-center justify-center bg-gradient-to-br from-primary-600 to-primary-400">
            <div class="text-center text-white max-w-2xl px-4">
                <h1 class="text-5xl font-bold mb-4" style="font-family: 'Playfair Display', serif">💻 Dinajpur IT Park</h1>
                <p class="text-xl mb-8">{{ __('general.premier_store') }}</p>
                <a href="{{ route('shop.index') }}" class="inline-block bg-white text-primary-600 px-8 py-3.5 rounded-xl text-lg font-bold hover:bg-primary-100 hover:-translate-y-0.5 transition-all duration-300">
                    {{ __('general.explore_plants') }}
                </a>
            </div>
        </div>
        @endforelse

        <!-- Slider dots -->
        @if($banners->count() > 1)
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
            @foreach($banners as $index => $banner)
            <button @click="current = {{ $index }}" class="w-2.5 h-2.5 rounded-full transition-all duration-300" :class="current === {{ $index }} ? 'bg-white scale-125' : 'bg-white/50 hover:bg-white/70'"></button>
            @endforeach
        </div>
        @endif
    </section>
@endif
