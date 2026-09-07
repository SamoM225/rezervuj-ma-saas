<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    @php
        use App\Models\BusinessSetting;
        use Illuminate\Support\Facades\Storage;

        $user = auth()->user();
        $isWorker = $user?->is_worker ?? false;
        $businessName = BusinessSetting::get('business_name', config('app.name'));
        $logoPath = BusinessSetting::get('business_logo_path');
        $logoUrl = $logoPath ? Storage::disk('public')->url($logoPath) : null;
        $roleLabel = match ($user?->role) {
            'superadmin' => __('ui.role_superadmin'),
            'admin' => __('ui.role_admin'),
            'worker' => __('ui.role_worker'),
            default => '',
        };
        $pageTitle = trim($__env->yieldContent('header_title', $isWorker ? __('ui.moja_nastenka') : __('ui.prehlad')));
    @endphp
    <title>{{ $pageTitle }} · {{ $businessName }}</title>
    <link rel="icon" href="{{ $logoUrl ?: "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='8' fill='%23C19A3E'/%3E%3Cpath d='M9 17l5 5 9-11' fill='none' stroke='%23fff' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E" }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>window.__cal = @json(__('calendar'));</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">
<div class="shell">
    <aside class="sidebar" :class="{ 'is-open': sidebarOpen }" aria-label="{{ __('ui.hlavna_navigacia') }}">
        <div class="sidebar-brand">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="">
            @else
                <div class="sidebar-mark">{{ mb_strtoupper(mb_substr($businessName, 0, 1)) }}</div>
            @endif
            <div>
                <div class="sidebar-brand-name">{{ $businessName }}</div>
                <div class="sidebar-brand-sub">{{ $isWorker ? __('ui.workspace_worker') : __('ui.workspace_admin') }}</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            @php
                $icon = fn (string $name) => match ($name) {
                    'home' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/>',
                    'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 3v3m8-3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1z"/>',
                    'clock' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>',
                    'users' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 19v-1a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v1m17 0v-1a4 4 0 0 0-3-3.87M13 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0zm2-3.13a3.5 3.5 0 0 1 0 6.26"/>',
                    'ban' => '<path stroke-linecap="round" stroke-linejoin="round" d="M5.6 5.6l12.8 12.8M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>',
                    'sparkle' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9zM19 16l.8 2.2L22 19l-2.2.8L19 22l-.8-2.2L16 19l2.2-.8z"/>',
                    'tag' => '<path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M3 5a2 2 0 0 1 2-2h5.6a2 2 0 0 1 1.4.6l8.4 8.4a2 2 0 0 1 0 2.8l-5.6 5.6a2 2 0 0 1-2.8 0L3.6 12A2 2 0 0 1 3 10.6z"/>',
                    'briefcase' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 6V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v1m-9 0h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2zm-2 6h16"/>',
                    'pin' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.3-6-10a6 6 0 0 1 12 0c0 4.7-6 10-6 10zm0-8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>',
                    'settings' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.3 4.3a1.7 1.7 0 0 1 3.4 0l.1.6a1.7 1.7 0 0 0 2.5 1l.5-.3a1.7 1.7 0 0 1 2.4 2.4l-.3.5a1.7 1.7 0 0 0 1 2.5l.6.1a1.7 1.7 0 0 1 0 3.4l-.6.1a1.7 1.7 0 0 0-1 2.5l.3.5a1.7 1.7 0 0 1-2.4 2.4l-.5-.3a1.7 1.7 0 0 0-2.5 1l-.1.6a1.7 1.7 0 0 1-3.4 0l-.1-.6a1.7 1.7 0 0 0-2.5-1l-.5.3a1.7 1.7 0 0 1-2.4-2.4l.3-.5a1.7 1.7 0 0 0-1-2.5l-.6-.1a1.7 1.7 0 0 1 0-3.4l.6-.1a1.7 1.7 0 0 0 1-2.5l-.3-.5a1.7 1.7 0 0 1 2.4-2.4l.5.3a1.7 1.7 0 0 0 2.5-1zM15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>',
                    default => '',
                };
                $navLink = function (string $route, string $label, string $iconName, string $active) use ($icon) {
                    $isActive = request()->routeIs(...explode('|', $active));
                    return '<a href="'.route($route).'" class="nav-link'.($isActive ? ' is-active' : '').'"'.($isActive ? ' aria-current="page"' : '').'>'
                        .'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">'.$icon($iconName).'</svg>'
                        .'<span>'.e($label).'</span></a>';
                };
            @endphp

            @if($isWorker)
                {!! $navLink('worker.dashboard', __('ui.prehlad'), 'home', 'worker.dashboard') !!}
                {!! $navLink('worker.calendar', __('ui.kalendar'), 'calendar', 'worker.calendar*') !!}
                {!! $navLink('worker.availability', __('ui.moja_dostupnost'), 'clock', 'worker.availability*') !!}
            @else
                {!! $navLink('admin.dashboard', __('ui.prehlad'), 'home', 'admin.dashboard') !!}
                {!! $navLink('admin.bookings.index', __('ui.kalendar'), 'calendar', 'admin.bookings*') !!}
                {!! $navLink('admin.customers.index', __('ui.zakaznici'), 'users', 'admin.customers*') !!}
                {!! $navLink('admin.blacklist.index', __('ui.blokovani_zakaznici'), 'ban', 'admin.blacklist*') !!}

                <div class="nav-section">{{ __('ui.ponuka') }}</div>
                {!! $navLink('admin.services', __('ui.sluzby'), 'sparkle', 'admin.services*') !!}
                {!! $navLink('admin.categories', __('ui.kategorie'), 'tag', 'admin.categories*') !!}
                {!! $navLink('admin.workers', __('ui.tim'), 'briefcase', 'admin.workers*') !!}
                {!! $navLink('admin.cities.index', __('ui.prevadzky'), 'pin', 'admin.cities*') !!}

                @if($user?->is_super_admin)
                    <div class="nav-section">{{ __('ui.system') }}</div>
                    {!! $navLink('admin.billing', __('ui.predplatne'), 'tag', 'admin.billing*') !!}
                    {!! $navLink('admin.settings', 'Nastavenia', 'settings', 'admin.settings*|admin.business-settings*|admin.email-templates*') !!}
                @endif
            @endif
        </nav>

        <div class="sidebar-user">
            <div class="avatar">{{ mb_strtoupper(mb_substr($user?->name ?? '?', 0, 1)) }}</div>
            <div style="flex:1;min-width:0">
                <div class="sidebar-user-name" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $user?->name }}</div>
                <div class="sidebar-user-role">{{ $roleLabel }}</div>
            </div>
            <a href="{{ route('account.2fa.show') }}" class="icon-btn" title="{{ __('ui.zabezpecenie_uctu') }}" aria-label="{{ __('ui.zabezpecenie_uctu') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 3.5V12c0 4.5-3 7.6-7 9-4-1.4-7-4.5-7-9V6.5z"/></svg>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="icon-btn" title="{{ __('ui.odhlasit_sa') }}" aria-label="{{ __('ui.odhlasit_sa') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17l5-5-5-5m5 5H9m4 7H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h7"/></svg>
                </button>
            </form>
        </div>
    </aside>

    <div class="overlay lg:hidden" x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" x-cloak></div>

    <div class="main">
        <header class="topbar">
            <div class="flex items-center gap-3">
                <button type="button" class="icon-btn lg:hidden" @click="sidebarOpen = true" aria-label="{{ __('ui.otvorit_menu') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>
                <h1 class="topbar-title">{{ $pageTitle }}</h1>
            </div>
            <div class="flex items-center gap-2">
                @yield('header_actions')
                <form method="POST" action="{{ route('language.switch') }}" class="flex items-center gap-1" aria-label="{{ __('ui.language') }}">
                    @csrf
                    <input type="hidden" name="admin" value="1">
                    @foreach(config('tenancy.locales', ['sk', 'cs', 'en']) as $uiLocale)
                        <button type="submit" name="locale" value="{{ $uiLocale }}" class="btn btn-sm {{ app()->getLocale() === $uiLocale ? 'btn-secondary' : 'btn-ghost' }}" style="text-transform:uppercase;font-size:.72rem;padding:.3rem .5rem" @disabled(app()->getLocale() === $uiLocale)>{{ $uiLocale }}</button>
                    @endforeach
                </form>
                <a href="{{ route('home') }}" class="btn btn-secondary btn-sm" target="_blank" rel="noopener" title="{{ __('ui.rezervacna_stranka') }}" aria-label="{{ __('ui.rezervacna_stranka') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 4h6v6m0-6L10 14M20 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h5"/></svg>
                    <span>{{ __('ui.rezervacna_stranka') }}</span>
                </a>
            </div>
        </header>

        <main class="content">
            @if(session('success') || session('status'))
                <div class="alert alert-ok" role="status">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <div>{{ session('success') ?? session('status') }}</div>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-bad" role="alert">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M12 8v5m0 3h.01M12 3l9 16H3z"/></svg>
                    <div>{{ session('error') }}</div>
                </div>
            @endif
            @if($errors->any() && !$errors->hasBag('bugReport'))
                <div class="alert alert-bad" role="alert">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M12 8v5m0 3h.01M12 3l9 16H3z"/></svg>
                    <div>
                        <strong>{{ __('ui.formular_sa_nepodarilo_ulozit') }}</strong>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
