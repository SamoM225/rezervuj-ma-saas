@extends('layouts.platform')

@section('title', __('auth.verify.title'))

@section('content')
<div class="auth-card">
    <div class="auth-brand">
        <div class="sidebar-mark" aria-hidden="true">✓</div>
        <div>
            <h1 class="auth-title">{{ __('auth.verify.title') }}</h1>
            <p class="auth-sub">{{ __('auth.verify.intro', ['email' => $email]) }}</p>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-good" role="status"><div>{{ session('status') }}</div></div>
    @endif
    @if ($errors->any())
        <div class="alert alert-bad" role="alert"><div>{{ $errors->first() }}</div></div>
    @endif

    <form method="POST" action="{{ \App\Support\Locales::route('register.confirm') }}" class="space-y-4">
        @csrf
        <div class="field">
            <label for="code">{{ __('auth.verify.code') }}</label>
            <input class="input mono" id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" autofocus maxlength="6" pattern="[0-9]{6}" required
                   style="text-align:center;font-size:1.35rem;letter-spacing:.3em;padding:.85rem">
        </div>
        <button type="submit" class="btn btn-primary btn-lg" style="width:100%">{{ __('auth.verify.submit') }}</button>
    </form>

    <form method="POST" action="{{ \App\Support\Locales::route('register.resend') }}" style="margin-top:1rem;text-align:center">
        @csrf
        <button type="submit" class="link" style="background:none;border:0;cursor:pointer">{{ __('auth.verify.resend') }}</button>
    </form>
</div>
@endsection
