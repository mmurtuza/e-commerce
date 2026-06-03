@props(['items' => []])

<nav class="flex text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
    <ol class="flex items-center gap-2 flex-wrap">
        <li>
            <a href="{{ route('home') }}" class="hover:text-primary-600 transition">{{ __('Home') }}</a>
        </li>
        @foreach($items as $item)
        <li class="flex items-center gap-2">
            <span class="text-gray-300">/</span>
            @if(isset($item['url']))
                <a href="{{ $item['url'] }}" class="hover:text-primary-600 transition">{{ $item['name'] }}</a>
            @else
                <span class="text-gray-700 font-medium">{{ $item['name'] }}</span>
            @endif
        </li>
        @endforeach
    </ol>
</nav>
