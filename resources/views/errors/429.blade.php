@extends('errors.layout')

@section('title', __('errors.429.title'))

@section('body')
    <div class="error-icon">🐢</div>
    <div class="error-code">429</div>
    <div class="divider"></div>
    <h1 class="error-title">{{ __('errors.429.header') }}</h1>
    <p class="error-message">
        {{ __('errors.429.message') }}
    </p>
    <div class="btn-group">
        <a href="javascript:location.reload()" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
            {{ __('errors.retry_button') }}
        </a>
        <a href="{{ url('/') }}" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            {{ __('errors.home_button') }}
        </a>
    </div>
@endsection
