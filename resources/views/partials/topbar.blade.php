@unless(request()->boolean('embed') || request()->header('Sec-Fetch-Dest') === 'iframe')
@php
    $tbName = \App\Models\BusinessSetting::get('business_name', config('app.name', 'Rezervácie'));
    $tbLogoPath = \App\Models\BusinessSetting::get('business_logo_path');
    $tbLogoUrl = $tbLogoPath ? \Illuminate\Support\Facades\Storage::url($tbLogoPath) : null;
    $tbLogoWidth = min(max((int) \App\Models\BusinessSetting::get('logo_width', 180), 60), 360);
    $tbPhone = \App\Models\BusinessSetting::get('support_phone');
    $tbTagline = $tagline ?? __('widget.reservation_title');
@endphp
<header class="bk-topbar">
    <div class="bk-topbar-inner">
        <a href="{{ route('home', ['tenant' => \App\Support\Tenancy::current()?->slug]) }}" class="bk-brand">
            @if($tbLogoUrl)
                <img src="{{ $tbLogoUrl }}" alt="{{ $tbName }}" class="bk-logo" style="width: {{ $tbLogoWidth }}px;">
            @else
                <span class="bk-brand-mark" aria-hidden="true">{{ mb_strtoupper(mb_substr($tbName, 0, 1)) }}</span>
            @endif
            <span>
                <span class="bk-brand-name">{{ $tbName }}</span>
                @if($tbTagline)<span class="bk-brand-tag">{{ $tbTagline }}</span>@endif
            </span>
        </a>
        <div class="bk-top-actions">
            @if($tbPhone)<a href="tel:{{ preg_replace('/\s+/', '', $tbPhone) }}" class="bk-top-link">{{ $tbPhone }}</a>@endif
            @if(!request()->routeIs('home'))
                <a href="{{ route('home', ['tenant' => \App\Support\Tenancy::current()?->slug]) }}" class="bk-top-link">{{ __('widget.book_now') }}</a>
            @endif
            <x-language-switcher />
            @auth
                <a href="{{ auth()->user()->is_worker ? route('worker.dashboard') : route('admin.dashboard') }}" class="bk-top-link">{{ __('widget.back_to_admin') }}</a>
            @endauth
        </div>
    </div>
</header>

@endunless
