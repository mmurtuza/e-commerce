@extends('errors.layout')

@section('title', __('errors.401.title'))

@section('body')
    <div class="error-icon">🔐</div>
    <div class="error-code">401</div>
    <div class="divider"></div>
    <h1 class="error-title">{{ __('errors.401.header') }}</h1>
    <p class="error-message">
        {{ __('errors.401.message') }}
    </p>
    <div class="btn-group">
        <a href="{{ route('login') }}" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
            {{ __('errors.401.login_button') }}
        </a>
        <a href="{{ url('/') }}" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            {{ __('errors.home_button') }}
        </a>
    </div>
@endsection
