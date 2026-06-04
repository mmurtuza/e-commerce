@extends('errors.layout')

@section('title', __('errors.404.title'))

@section('body')
    <div class="error-icon">🌿</div>
    <div class="error-code">404</div>
    <div class="divider"></div>
    <h1 class="error-title">{{ __('errors.404.header') }}</h1>
    <p class="error-message">
        {{ __('errors.404.message') }}
    </p>
    <div class="btn-group">
        <a href="{{ url('/') }}" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            {{ __('errors.home_button') }}
        </a>
        <a href="{{ route('shop.index') }}" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            {{ __('errors.404.shop_button') }}
        </a>
    </div>
@endsection
