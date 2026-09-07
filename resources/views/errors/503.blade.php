@extends(\App\Support\Tenancy::check() ? 'layouts.app' : 'layouts.site')

@section('title', 'Stránka je dočasne nedostupná · '.\App\Models\BusinessSetting::get('business_name', config('app.name')))
@section('robots', 'noindex, nofollow')

@section('content')
@if(\App\Support\Tenancy::check())@include('partials.topbar', ['tagline' => null])@endif
<div class="bk-page">
    <div class="bk-shell">
        <div class="bk-status">
            <div class="bk-status-icon is-bad">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 8v5m0 3h.01"/></svg>
            </div>
            <h1>Stránka je dočasne nedostupná</h1>
            <p class="lead">Prebieha krátka údržba. Skúste to prosím o pár minút.</p>
            <p class="meta" style="color:var(--bk-muted);font-size:.8rem">Kód chyby 503</p>
            <div class="bk-btn-row">
                <a class="bk-btn is-primary" href="{{ \App\Support\Tenancy::check() ? route('home', ['tenant' => \App\Support\Tenancy::current()?->slug]) : url('/') }}">Späť na rezerváciu</a>
                <button type="button" class="bk-btn" onclick="history.back()">Predchádzajúca stránka</button>
            </div>
        </div>
    </div>
</div>
@endsection
