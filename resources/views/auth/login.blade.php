@extends('layouts.auth')

@section('title', __('auth.login.title'))

@php $businessName = \App\Models\BusinessSetting::get('business_name', config('app.name')); @endphp

@section('content')
<div class="auth-card">
    <div class="auth-brand">
        @php $authLogo = \App\Models\BusinessSetting::get('business_logo_path'); @endphp
        @if($authLogo)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($authLogo) }}" alt="{{ $businessName }}" style="width:3.25rem;height:3.25rem;object-fit:contain;border-radius:.75rem;background:var(--gold-soft);padding:.35rem">
        @else
            <div class="sidebar-mark">{{ mb_strtoupper(mb_substr($businessName, 0, 1)) }}</div>
        @endif
        <div>
            <h1 class="auth-title">{{ __('auth.login.title') }}</h1>
            <p class="auth-sub">{{ $businessName }} · {{ __('auth.login.subtitle') }}</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-bad" role="alert">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M12 8v5m0 3h.01M12 3l9 16H3z"/></svg>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
        @csrf
        <div class="field">
            <label for="email">{{ __('auth.login.email') }}</label>
            <input class="input" id="email" name="email" type="email" autocomplete="username" required autofocus value="{{ old('email') }}">
        </div>
        <div class="field">
            <label for="password">{{ __('auth.login.password') }}</label>
            <input class="input" id="password" name="password" type="password" autocomplete="current-password" required>
        </div>
        <label class="check" style="padding:.25rem 0">
            <input type="checkbox" name="remember" value="1">
            <span>{{ __('auth.login.remember') }}</span>
        </label>
        <button type="submit" class="btn btn-primary btn-lg" style="width:100%">{{ __('auth.login.submit') }}</button>
    </form>

    @if (session('status'))
        <div class="alert alert-good" role="status" style="margin-top:1rem"><div>{{ session('status') }}</div></div>
    @endif
    <p class="hint" style="margin-top:1.25rem;text-align:center">
        <a href="{{ route('password.request') }}" class="link">{{ __('auth.reset.forgot_link') }}</a>
        · <a href="{{ route('home') }}" class="link">{{ __('auth.login.back_to_booking') }}</a>
    </p>
</div>
@endsection
