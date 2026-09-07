@extends(\App\Support\Tenancy::check() ? 'layouts.app' : 'layouts.site')

@section('title', 'Formulár vypršal · '.\App\Models\BusinessSetting::get('business_name', config('app.name')))
@section('robots', 'noindex, nofollow')

@section('content')
@if(\App\Support\Tenancy::check())@include('partials.topbar', ['tagline' => null])@endif
<div class="bk-page">
    <div class="bk-shell">
        <div class="bk-status">
            <div class="bk-status-icon is-bad">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 8v5m0 3h.01"/></svg>
            </div>
            <h1>Formulár vypršal</h1>
            <p class="lead">Stránka bola otvorená príliš dlho a bezpečnostný token už neplatí. Obnovte ju a skúste to znova.</p>
            <p class="meta" style="color:var(--bk-muted);font-size:.8rem">Kód chyby 419</p>
            <div class="bk-btn-row">
                <a class="bk-btn is-primary" href="{{ \App\Support\Tenancy::check() ? route('home', ['tenant' => \App\Support\Tenancy::current()?->slug]) : url('/') }}">Späť na rezerváciu</a>
                <button type="button" class="bk-btn" onclick="history.back()">Predchádzajúca stránka</button>
            </div>
        </div>
    </div>
</div>
@endsection
