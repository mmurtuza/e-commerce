@extends('errors.layout')

@section('title', __('errors.403.title'))

@section('body')
    <div class="error-icon">🚫</div>
    <div class="error-code">403</div>
    <div class="divider"></div>
    <h1 class="error-title">{{ __('errors.403.header') }}</h1>
    <p class="error-message">
        {{ __('errors.403.message') }}
    </p>
    <div class="btn-group">
        <a href="{{ url('/') }}" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            {{ __('errors.home_button') }}
        </a>
        <a href="javascript:history.back()" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            {{ __('errors.back_button') }}
        </a>
    </div>
@endsection
