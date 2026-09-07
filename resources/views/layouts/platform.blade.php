<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="@yield('robots', 'noindex, nofollow')">
    <title>@yield('title', 'rezervuj-ma') · rezervuj-ma.online</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='8' fill='%231E6B5E'/%3E%3Crect x='6' y='6' width='20' height='3.1' rx='1.55' fill='%23fff' opacity='.55'/%3E%3Crect x='6' y='12.7' width='8.6' height='4.2' rx='2.1' fill='%23fff' opacity='.38'/%3E%3Crect x='17.4' y='12.7' width='8.6' height='4.2' rx='2.1' fill='%23E4B457'/%3E%3Crect x='6' y='19.4' width='8.6' height='4.2' rx='2.1' fill='%23fff' opacity='.38'/%3E%3Crect x='17.4' y='19.4' width='8.6' height='4.2' rx='2.1' fill='%23fff' opacity='.38'/%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --accent: #C19A3E; --accent-rgb: 193,154,62; --booking-bg: #FBF7F0; --ink: #211B14; }
        .auth-shell { min-height: 100vh; display: grid; place-items: center; padding: 2rem 1rem; background: radial-gradient(60rem 30rem at 50% -10%, rgba(var(--accent-rgb), 0.16), transparent 60%), var(--booking-bg); }
        .auth-card.wide { max-width: 40rem; }
        .check-legal { display: flex; gap: .65rem; align-items: flex-start; font-size: .9rem; line-height: 1.45; padding: .35rem 0; }
        .check-legal input { margin-top: .2rem; flex: none; }
        .grid-2 { display: grid; gap: 1rem; grid-template-columns: 1fr; }
        @media (min-width: 40rem) { .grid-2 { grid-template-columns: 1fr 1fr; } }
    </style>
</head>
<body>
    <div class="auth-shell">
        @yield('content')
    </div>
    @stack('scripts')
</body>
</html>
