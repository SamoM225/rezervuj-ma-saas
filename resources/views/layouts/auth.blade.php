<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', __('auth.login.title')) · {{ \App\Models\BusinessSetting::get('business_name', config('app.name')) }}</title>
    @php $faviconLogo = \App\Models\BusinessSetting::get('business_logo_path'); @endphp
    <link rel="icon" href="{{ $faviconLogo ? \Illuminate\Support\Facades\Storage::url($faviconLogo) : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='8' fill='%23C19A3E'/%3E%3Cpath d='M9 17l5 5 9-11' fill='none' stroke='%23fff' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E" }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.theme-vars')
    <style>
        /* the login screen wears the same colours as the public booking page */
        .auth-shell { background: radial-gradient(60rem 30rem at 50% -10%, rgba(var(--accent-rgb), 0.16), transparent 60%), var(--booking-bg); }
    </style>
</head>
<body>
    <div class="auth-shell">
        @yield('content')
    </div>
    @stack('scripts')
</body>
</html>
