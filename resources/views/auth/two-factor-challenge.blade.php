@extends('layouts.auth')

@section('title', __('auth.otp.title'))

@section('content')
<div class="auth-card">
    <div class="auth-brand">
        <div class="sidebar-mark" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:1.25rem;height:1.25rem"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 3.5V12c0 4.5-3 7.6-7 9-4-1.4-7-4.5-7-9V6.5z"/></svg>
        </div>
        <div>
            <h1 class="auth-title">{{ __('auth.otp.title') }}</h1>
            <p class="auth-sub">{{ $method === 'email' ? __('auth.otp.email_intro') : __('auth.otp.totp_intro') }}</p>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-good" role="status"><div>{{ session('status') }}</div></div>
    @endif
    @if ($errors->any())
        <div class="alert alert-bad" role="alert">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M12 8v5m0 3h.01M12 3l9 16H3z"/></svg>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <form method="POST" action="{{ route('2fa.verify') }}" class="space-y-4">
        @csrf
        <div class="field">
            <label for="code">{{ __('auth.verify.code') }}</label>
            <input class="input mono" id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" autofocus maxlength="16"
                   style="text-align:center;font-size:1.35rem;letter-spacing:.3em;padding:.85rem">
        </div>
        @if ($method === 'email')
            <label class="check" style="padding:.25rem 0">
                <input type="checkbox" name="trust_device" value="1" checked>
                <span>{{ __('auth.otp.trust_device') }}</span>
            </label>
        @endif
        <button type="submit" class="btn btn-primary btn-lg" style="width:100%">{{ __('auth.verify.submit') }}</button>
    </form>

    @if ($method === 'email')
        <form method="POST" action="{{ route('2fa.resend') }}" style="margin-top:1rem;text-align:center">
            @csrf
            <button type="submit" class="link" style="background:none;border:0;cursor:pointer">{{ __('auth.otp.resend') }}</button>
        </form>
    @else
        <p class="hint" style="margin-top:1.25rem;text-align:center">{{ __('auth.login.no_app_hint') }}</p>
    @endif
    <p class="hint" style="margin-top:.5rem;text-align:center"><a href="{{ route('login') }}" class="link">{{ __('auth.login.back_to_login') }}</a></p>
</div>
@endsection
