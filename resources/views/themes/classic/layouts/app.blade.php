@php
    $theme = \App\Models\Setting::get('theme', 'default');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr" data-theme="{{ $theme }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @inject('seoService', 'App\Services\SeoService')
    @php
        $meta = $seoService->getMetaData();
        $siteName = $meta['siteName'];
    @endphp

    <title>{{ $meta['title'] }}</title>
    <meta name="description" content="{{ $meta['metaDescription'] }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ $meta['ogType'] }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $meta['ogTitle'] }}">
    <meta property="og:description" content="{{ $meta['ogDescription'] }}">
    <meta property="og:image" content="{{ $meta['ogImage'] }}">
    <meta property="og:site_name" content="{{ $siteName }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $meta['ogTitle'] }}">
    <meta property="twitter:description" content="{{ $meta['ogDescription'] }}">
    <meta property="twitter:image" content="{{ $meta['ogImage'] }}">

    <link rel="alternate" hreflang="bn" href="{{ url()->current() }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite("resources/css/themes/{$theme}.css")
    @stack('styles')

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f3f4f6; /* bg-gray-100 */
        }
    </style>
</head>

<body class="antialiased">

    <!-- Navbar -->
    <x-navbar :site-name="$siteName" />

    @php
        $flashTypes = [
            'success' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-800', 'btn' => 'text-green-400 hover:text-green-700'],
            'error' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-800', 'btn' => 'text-red-400 hover:text-red-700'],
            'warning' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'text' => 'text-yellow-800', 'btn' => 'text-yellow-400 hover:text-yellow-700'],
            'info' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-800', 'btn' => 'text-blue-400 hover:text-blue-700'],
        ];
    @endphp
    @foreach($flashTypes as $type => $classes)
        @if(session($type))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div
                    class="{{ $classes['bg'] }} {{ $classes['border'] }} {{ $classes['text'] }} border rounded-xl px-4 py-3 flex justify-between items-start gap-3">
                    <span>{{ session($type) }}</span>
                    <button onclick="this.parentElement.parentElement.remove()"
                        class="{{ $classes['btn'] }} text-xl leading-none mt-0.5">&times;</button>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>


    <!-- WhatsApp float -->
    <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp') }}?text={{ urlencode(__('general.whatsapp_message')) }}"
        target="_blank" rel="noopener"
        class="fixed bottom-6 right-6 bg-green-500 hover:bg-green-600 text-white p-3.5 rounded-full shadow-lg hover:scale-110 transition z-50">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
            <path
                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
        </svg>
    </a>
    <!-- Global Toast / Snack Container -->
    <div x-data="{ 
            toasts: [], 
            addToast(message, type = 'success') {
                const id = Date.now();
                this.toasts.push({ id, message, type });
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 3000);
            } 
         }"
         @toast.window="addToast($event.detail.message, $event.detail.type)"
         class="fixed bottom-6 left-6 z-50 flex flex-col gap-2 pointer-events-none max-w-sm w-full"
         x-cloak>
        
        <template x-for="toast in toasts" :key="toast.id">
            <div x-transition:enter="transition ease-out duration-300 transform translate-y-2 opacity-0"
                 x-transition:enter-start="transform translate-y-2 opacity-0"
                 x-transition:enter-end="transform translate-y-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200 transform translate-y-0 opacity-100"
                 x-transition:leave-start="transform translate-y-0 opacity-100"
                 x-transition:leave-end="transform translate-y-2 opacity-0"
                 class="pointer-events-auto bg-primary-600 text-white px-4 py-3 rounded-xl shadow-lg flex items-center justify-between gap-3 border border-primary-400/20">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🛒</span>
                    <span x-text="toast.message" class="text-sm font-medium"></span>
                </div>
                <button @click="toasts = toasts.filter(t => t.id !== toast.id)" class="text-white/70 hover:text-white">&times;</button>
            </div>
        </template>
    </div>

    <x-footer />
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetch('/cart/count', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    document.querySelectorAll('.cart-count').forEach(el => el.textContent = data.count ?? 0);
                }).catch(() => { });
        });
    </script>
</body>

</html>