@extends('errors.layout')

@section('title', __('errors.503.title'))

@section('body')
    <div class="error-icon">🌱</div>
    <div class="error-code" style="font-size: clamp(4rem, 14vw, 7rem);">{{ __('errors.503.badge') }}</div>
    <div class="divider"></div>
    <h1 class="error-title">{{ __('errors.503.header') }}</h1>
    <p class="error-message">
        {{ __('errors.503.message') }}
    </p>

    {{-- Countdown-style progress dots --}}
    <div style="display: flex; justify-content: center; gap: 0.5rem; margin-bottom: 2rem;">
        <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--primary-400); animation: bounce 1.4s ease-in-out infinite;"></span>
        <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--primary-500); animation: bounce 1.4s ease-in-out 0.2s infinite;"></span>
        <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--primary-600); animation: bounce 1.4s ease-in-out 0.4s infinite;"></span>
    </div>

    <style>
        @keyframes bounce {
            0%, 80%, 100% { transform: translateY(0); opacity: 0.4; }
            40% { transform: translateY(-8px); opacity: 1; }
        }
    </style>

    <div class="btn-group">
        <a href="javascript:location.reload()" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
            {{ __('errors.retry_button') }}
        </a>
    </div>
@endsection
