@props(['siteName'])

@php 
    $showCategoryMenu = \App\Models\Setting::get('show_category_menu');
    $navCategories = $showCategoryMenu ? \App\Models\Category::whereNull('parent_id')->get() : collect();
@endphp

<nav class="bg-white shadow-sm sticky top-0 z-50" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-4">

        <!-- Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
            @php $logo = \App\Models\Setting::get('site_logo'); @endphp
            @if($logo)
                <img src="{{ asset('storage/' . $logo) }}" alt="{{ config('app.name') }}"
            class="h-16 w-auto object-contain"> @else
                    <span class="text-2xl font-bold text-primary-700" style="font-family:'Playfair Display',serif">
                        {{ $siteName }}
                    </span>
                @endif
        </a>
        <!-- Search (desktop) -->
        <form action="{{ route('search.index') }}" method="GET" class="hidden md:flex flex-1 max-w-md">
            <div
                class="flex w-full border border-gray-200 rounded-xl overflow-hidden focus-within:border-primary-400 focus-within:ring-1 focus-within:ring-primary-400 transition">
                <input type="text" name="q" value="{{ request('q') }}"
                    placeholder="{{ __('general.search_placeholder') }}"
                    class="flex-1 px-4 py-2 text-sm outline-none bg-transparent">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>
        </form>

        <!-- Desktop right section -->
        <div class="hidden md:flex items-center gap-5">
            <!-- Nav links -->
            <a href="{{ route('home') }}"
                class="text-sm font-medium text-gray-700 hover:text-primary-600 transition">
                {{ __('general.home') }}
            </a>
            @if($showCategoryMenu && $navCategories->count() > 0)
                <div class="relative group" x-data="{ open: false }">
                    <button @mouseenter="open = true" @mouseleave="open = false"
                        class="text-sm font-medium text-gray-700 hover:text-primary-600 transition flex items-center gap-1">
                        {{ __('Categories') }}
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open" x-cloak @mouseenter="open = true" @mouseleave="open = false" x-transition class="absolute left-0 pt-2 w-48 z-50">
                        <div class="bg-white rounded-xl shadow-lg border border-gray-100 py-2">
                            @foreach($navCategories as $cat)
                                <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary-600">{{ $cat->name }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            <a href="{{ route('shop.index') }}"
                class="text-sm font-medium text-gray-700 hover:text-primary-600 transition">
                {{ __('general.shop') }}
            </a>
            <a href="{{ route('blog.index') }}"
                class="text-sm font-medium text-gray-700 hover:text-primary-600 transition">
                {{ __('general.blog') }}
            </a>

            <!-- Language switcher -->
            <x-language-switcher />

            <!-- Divider -->
            <span class="w-px h-5 bg-gray-200"></span>

            <!-- Wishlist -->
            <a href="{{ auth()->check() ? route('wishlist.index') : route('login') }}"
                class="relative text-gray-600 hover:text-primary-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </a>

            <!-- Cart -->
            <a href="{{ route('cart.index') }}" class="relative text-gray-600 hover:text-primary-600 transition"
                id="cart-icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span
                    class="cart-count absolute -top-2 -right-2 bg-primary-600 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center leading-none">0</span>
            </a>

            <!-- User menu -->
            @auth
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center gap-1.5 text-sm text-gray-700 hover:text-primary-600 transition font-medium">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-7 h-7 rounded-full object-cover">
                        @else
                            <div class="w-7 h-7 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold text-xs">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <span>{{ auth()->user()->name }}</span>
                        <svg class="w-3 h-3" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak @click.away="open = false" x-transition
                        class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                        <a href="{{ route('customer.dashboard') }}"
                            class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            {{ __('general.dashboard') }}
                        </a>
                        <a href="{{ route('customer.orders') }}"
                            class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            {{ __('general.my_orders') }}
                        </a>
                        <a href="{{ route('customer.profile') }}"
                            class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            {{ __('general.profile') }}
                        </a>
                        <hr class="my-1 border-gray-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                {{ __('general.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}"
                    class="bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition">
                    {{ __('general.login') }}
                </a>
            @endauth
        </div>

        <!-- Mobile: cart + hamburger -->
        <div class="flex md:hidden items-center gap-3">
            <a href="{{ route('cart.index') }}" class="relative text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span
                    class="cart-count absolute -top-2 -right-2 bg-primary-600 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center leading-none">0</span>
            </a>
            <button @click="mobileOpen = !mobileOpen" class="text-gray-700 p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="mobileOpen" x-cloak x-collapse class="md:hidden border-t border-gray-100 bg-white">
        <div class="px-4 py-3 space-y-1">
            <!-- Mobile search -->
            <form action="{{ route('search.index') }}" method="GET" class="mb-3">
                <div class="flex border border-gray-200 rounded-xl overflow-hidden">
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="{{ __('general.search_placeholder') }}"
                        class="flex-1 px-4 py-2 text-sm outline-none">
                    <button type="submit" class="bg-primary-600 text-white px-4 py-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </form>

            <a href="{{ route('home') }}"
                class="flex items-center gap-2 px-2 py-2.5 text-gray-700 hover:text-primary-600 text-sm font-medium">{{ __('general.home') }}</a>
            
            @if($showCategoryMenu && $navCategories->count() > 0)
                <div x-data="{ catOpen: false }">
                    <button @click="catOpen = !catOpen" class="flex items-center justify-between w-full px-2 py-2.5 text-gray-700 hover:text-primary-600 text-sm font-medium">
                        <span>{{ __('Categories') }}</span>
                        <svg class="w-4 h-4 transition-transform" :class="catOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="catOpen" x-collapse class="pl-4 pr-2 space-y-1 pb-2">
                        @foreach($navCategories as $cat)
                            <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="block px-2 py-2 text-sm text-gray-600 hover:text-primary-600">{{ $cat->name }}</a>
                        @endforeach
                    </div>
                </div>
            @endif

            <a href="{{ route('shop.index') }}"
                class="flex items-center gap-2 px-2 py-2.5 text-gray-700 hover:text-primary-600 text-sm font-medium">{{ __('general.shop') }}</a>
            <a href="{{ route('blog.index') }}"
                class="flex items-center gap-2 px-2 py-2.5 text-gray-700 hover:text-primary-600 text-sm font-medium">{{ __('general.blog') }}</a>

            <div class="px-2 py-2">
                <x-language-switcher />
            </div>

            <hr class="border-gray-100 my-1">

            @auth
                <a href="{{ route('customer.dashboard') }}"
                    class="flex items-center gap-2 px-2 py-2.5 text-gray-700 hover:text-primary-600 text-sm">{{ __('general.my_account') }}</a>
                <a href="{{ route('customer.orders') }}"
                    class="flex items-center gap-2 px-2 py-2.5 text-gray-700 hover:text-primary-600 text-sm">{{ __('general.my_orders') }}</a>
                <form method="POST" action="{{ route('logout') }}" class="px-2 py-1">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 font-medium">{{ __('general.logout') }}</button>
                </form>
            @else
                <a href="{{ route('login') }}"
                    class="block mx-2 my-1 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl text-center transition">
                    {{ __('general.login') }}
                </a>
                <a href="{{ route('register') }}"
                    class="block mx-2 my-1 border border-primary-600 text-primary-600 text-sm font-medium px-4 py-2.5 rounded-xl text-center hover:bg-primary-50 transition">
                    {{ __('general.register') }}
                </a>
            @endauth
        </div>
    </div>
</nav>
