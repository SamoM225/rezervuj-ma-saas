@extends('layouts.auth')

@section('title', __('auth.reset.reset_title'))

@section('content')
<div class="auth-card">
    <div class="auth-brand">
        <div class="sidebar-mark" aria-hidden="true">•••</div>
        <div>
            <h1 class="auth-title">{{ __('auth.reset.reset_title') }}</h1>
            <p class="auth-sub">{{ $email }}</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-bad" role="alert"><div>{{ $errors->first() }}</div></div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">
        <div class="field">
            <label for="password">{{ __('auth.reset.new_password') }}</label>
            <input class="input" id="password" name="password" type="password" autocomplete="new-password" required minlength="10" autofocus>
            <p class="hint">{{ __('auth.register.password_hint') }}</p>
        </div>
        <div class="field">
            <label for="password_confirmation">{{ __('auth.reset.confirm_password') }}</label>
            <input class="input" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required minlength="10">
        </div>
        <button type="submit" class="btn btn-primary btn-lg" style="width:100%">{{ __('auth.reset.submit') }}</button>
    </form>
</div>
@endsection
