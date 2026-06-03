@props(['banners'])

<section class="relative overflow-hidden bg-primary-600" x-data="{ current: 0, slides: {{ $banners->count() ?: 1 }} }" x-init="setInterval(() => current = (current + 1) % slides, 5000)">
    @forelse($banners as $index => $banner)
    <div x-show="current === {{ $index }}" class="relative min-h-[500px] md:min-h-[600px] flex items-center" style="background-image: url('{{ Storage::url($banner->image) }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="relative max-w-7xl mx-auto px-4 text-white">
            <h1 class="text-4xl md:text-6xl font-bold mb-4" style="font-family: 'Playfair Display', serif">
                {{ $banner->translate()?->title ?? 'Welcome to Dinajpur IT Park' }}
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-gray-200">{{ $banner->translate()?->subtitle ?? 'Your Ultimate Computer Accessories Store in Bangladesh' }}</p>
            <a href="{{ $banner->link ?? route('shop.index') }}"
                class="inline-block bg-primary-400 text-white px-8 py-3 rounded-full text-lg font-semibold hover:bg-white hover:text-primary-600 transition-all duration-300">
                {{ $banner->translate()?->button_text ?? __('general.shop_now') }}
            </a>
        </div>
    </div>
    @empty
    <div class="min-h-[500px] flex items-center justify-center bg-gradient-to-br from-primary-600 to-primary-400">
        <div class="text-center text-white max-w-2xl px-4">
            <h1 class="text-5xl font-bold mb-4" style="font-family: 'Playfair Display', serif">💻 Dinajpur IT Park</h1>
            <p class="text-xl mb-8">{{ __('general.premier_store') }}</p>
            <a href="{{ route('shop.index') }}" class="inline-block bg-white text-primary-600 px-8 py-3 rounded-full text-lg font-semibold hover:bg-primary-300 transition-all duration-300">
                {{ __('general.explore_plants') }}
            </a>
        </div>
    </div>
    @endforelse

    <!-- Slider dots -->
    @if($banners->count() > 1)
    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
        @foreach($banners as $index => $banner)
        <button @click="current = {{ $index }}" class="w-2 h-2 rounded-full transition" :class="current === {{ $index }} ? 'bg-white' : 'bg-white/50'"></button>
        @endforeach
    </div>
    @endif
</section>
