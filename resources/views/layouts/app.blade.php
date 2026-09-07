<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $theme = \App\Models\Setting::getThemeColors();
        $accent = $theme['main_accent'];
        $bizName = \App\Models\BusinessSetting::get('business_name', config('app.name', 'Rezervácia'));
        $bizAddr = \App\Models\BusinessSetting::get('business_address');
        $seoTitle = trim($__env->yieldContent('title', __('customer.meta.default_title', ['name' => $bizName])));
        $seoDesc = __('customer.meta.default_description', ['name' => $bizName.($bizAddr ? ' ('.$bizAddr.')' : '')]);
    @endphp
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDesc }}">
    <meta name="robots" content="{{ trim($__env->yieldContent('robots', 'noindex, nofollow')) }}">
    <meta name="theme-color" content="{{ $accent }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $bizName }}">
    <meta property="og:locale" content="{{ __('customer.meta.og_locale') }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDesc }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <link rel="canonical" href="{{ url()->current() }}">
    @php $faviconLogo = \App\Models\BusinessSetting::get('business_logo_path'); @endphp
    <link rel="icon" href="{{ $faviconLogo ? \Illuminate\Support\Facades\Storage::url($faviconLogo) : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='8' fill='".urlencode($accent)."'/%3E%3Cpath d='M9 17l5 5 9-11' fill='none' stroke='%23fff' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E" }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.theme-vars')
    @stack('styles')
</head>
<body>
    @yield('content')

    @include('partials.bug-report-modal')
    @unless(request()->boolean('embed') || request()->header('Sec-Fetch-Dest') === 'iframe')
        @include('partials.cookie-notice')
    @endunless

    <script>
        (function () {
            const modal = document.getElementById('bug-report-modal');
            if (!modal) return;
            const open = () => { modal.hidden = false; document.body.style.overflow = 'hidden'; modal.querySelector('input,textarea')?.focus(); };
            const close = () => { modal.hidden = true; document.body.style.overflow = ''; };
            document.querySelectorAll('[data-bug-report-trigger]').forEach(b => b.addEventListener('click', open));
            modal.querySelectorAll('[data-bug-report-dismiss]').forEach(b => b.addEventListener('click', close));
            document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.hidden) close(); });
            if (@json($errors->hasBag('bugReport') && $errors->bugReport->any())) open();
        })();
    </script>
    @stack('scripts')
</body>
</html>
