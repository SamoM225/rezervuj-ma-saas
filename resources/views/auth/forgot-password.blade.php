@extends('layouts.auth')

@section('title', __('auth.reset.forgot_title'))

@section('content')
<div class="auth-card">
    <div class="auth-brand">
        <div class="sidebar-mark" aria-hidden="true">?</div>
        <div>
            <h1 class="auth-title">{{ __('auth.reset.forgot_title') }}</h1>
            <p class="auth-sub">{{ __('auth.reset.forgot_intro') }}</p>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-good" role="status"><div>{{ session('status') }}</div></div>
    @endif
    @if ($errors->any())
        <div class="alert alert-bad" role="alert"><div>{{ $errors->first() }}</div></div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <div class="field">
            <label for="email">E-mail</label>
            <input class="input" id="email" name="email" type="email" autocomplete="username" required autofocus value="{{ old('email') }}">
        </div>
        <button type="submit" class="btn btn-primary btn-lg" style="width:100%">{{ __('auth.reset.send_link') }}</button>
    </form>

    <p class="hint" style="margin-top:1.25rem;text-align:center"><a href="{{ route('login') }}" class="link">{{ __('auth.login.back_to_login') }}</a></p>
</div>
@endsection
