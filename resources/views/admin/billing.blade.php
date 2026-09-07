@extends('layouts.admin')

@section('title', __('ui.predplatne'))
@section('header_title', __('ui.predplatne'))

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">{{ __('ui.predplatne') }}</h1>
        <p class="page-sub">{{ __('ui.free_je_zadarmo_navzdy_do_50') }}</p>
    </div>
</div>

@if ($errors->has('billing'))
    <div class="alert alert-bad" role="alert"><div>{{ $errors->first('billing') }}</div></div>
@endif

<div class="grid-2" style="display:grid;gap:1.25rem;grid-template-columns:repeat(auto-fit,minmax(18rem,1fr))">
    <section class="card">
        <h2 class="card-title">{{ __('ui.vas_plan') }} <strong>{{ $isPro ? 'Pro' : 'Free' }}</strong></h2>
        @if ($isPro)
            <p>{{ __('ui.pro_je_aktivne_do') }} <strong>{{ $tenant->pro_until?->translatedFormat('j. n. Y') }}</strong>.</p>
            @if ($subscription)
                <p class="hint">{{ __('ui.paypal_subscription') }} {{ $subscription->provider_id }} · {{ $subscription->status }} · {{ number_format((float) $subscription->amount, 2, ',', ' ') }} {{ $subscription->currency }} / {{ str_ends_with($subscription->plan_key, 'yearly') ? 'rok' : 'mesiac' }}</p>
                @if ($subscription->isActive())
                    <form method="POST" action="{{ route('admin.billing.cancel') }}" onsubmit="return confirm(@js(__('ui.zrusit_predplatne_pro_ostane_aktivne_do')))" style="margin-top:1rem">
                        @csrf
                        <button type="submit" class="btn btn-ghost">{{ __('ui.zrusit_predplatne') }}</button>
                    </form>
                @else
                    <p class="hint">{{ __('ui.predplatne_je_zrusene_neobnovi_sa') }}</p>
                @endif
            @endif
        @else
            <p>{{ __('ui.tento_mesiac_ste_vyuzili') }} <strong>{{ $used }} / {{ $limit }}</strong> {{ __('ui.rezervacii') }}</p>
            <div style="height:.5rem;border-radius:999px;background:rgba(0,0,0,.08);overflow:hidden;margin:.5rem 0 1rem">
                <div style="height:100%;width:{{ min(100, (int) round($used / max(1, $limit) * 100)) }}%;background:var(--accent)"></div>
            </div>
            <p class="hint">{{ __('ui.po_vycerpani_limitu_sa_online_rezervacie') }}</p>
        @endif
    </section>

    <section class="card">
        <h2 class="card-title">{{ __('ui.pro_5_mesacne_alebo_50_rocne') }}</h2>
        <ul class="hint" style="margin:.5rem 0 1rem;padding-left:1.1rem;line-height:1.7">
            <li>{{ __('ui.neobmedzene_rezervacie_pracovnici_aj_prevadzky') }}</li>
            <li>{{ __('ui.bez_odkazu_rezervuj_ma_na_stranke') }}</li>
            <li>{{ __('ui.vlastne_logo_farby_a_uvodny_obrazok') }}</li>
            <li>{{ __('ui.editovatelne_e_mailove_sablony') }}</li>
            <li>{{ __('ui.viacjazycna_rezervacna_stranka_a_widget_na') }}</li>
            <li>{{ __('ui.zrusenie_kedykolvek_bez_viazanosti') }}</li>
        </ul>

        @if (! $isPro)
            @if ($billingEnabled)
            <div style="display:flex;gap:.5rem;margin-bottom:1rem">
                <a href="{{ route('admin.billing', ['currency' => 'EUR']) }}" class="btn {{ $currency === 'EUR' ? 'btn-primary' : 'btn-ghost' }}">EUR</a>
                <a href="{{ route('admin.billing', ['currency' => 'USD']) }}" class="btn {{ $currency === 'USD' ? 'btn-primary' : 'btn-ghost' }}">USD</a>
            </div>
            @endif

            @if ($paypalConfigured)
                <p class="hint" style="margin-bottom:.35rem">{{ __('ui.monthly') }} — {{ number_format((float) $plans['monthly']['amount'], 2, ',', ' ') }} {{ $currency }}</p>
                <div id="paypal-monthly" data-plan="{{ $plans['monthly']['id'] }}" data-key="{{ $planKeys['monthly'] }}"></div>
                <p class="hint" style="margin:1rem 0 .35rem">{{ __('ui.yearly') }} — {{ number_format((float) $plans['yearly']['amount'], 2, ',', ' ') }} {{ $currency }} {{ __('ui.two_months_free') }}</p>
                <div id="paypal-yearly" data-plan="{{ $plans['yearly']['id'] }}" data-key="{{ $planKeys['yearly'] }}"></div>
                <p id="paypal-status" class="hint" style="margin-top:.75rem" aria-live="polite"></p>
            @elseif (! $billingEnabled)
                <p class="hint">{{ __('site.pricing.payment_soon') }}</p>
            @else
                <div class="alert alert-bad"><div>{{ __('ui.platby_este_nie_su_nastavene_chybaju') }}</div></div>
            @endif
        @endif
    </section>
</div>
@endsection

@if (! $isPro && $paypalConfigured)
@push('scripts')
<script src="{{ $sdkHost }}/sdk/js?client-id={{ urlencode($clientId) }}&vault=true&intent=subscription&currency={{ $currency }}&components=buttons" data-namespace="paypalSdk"></script>
<script>
(function () {
    var status = document.getElementById('paypal-status');
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    ['paypal-monthly', 'paypal-yearly'].forEach(function (id) {
        var el = document.getElementById(id);
        if (!el || !window.paypalSdk) return;
        paypalSdk.Buttons({
            style: { shape: 'pill', color: 'gold', layout: 'horizontal', label: 'subscribe', tagline: false },
            createSubscription: function (data, actions) {
                return actions.subscription.create({ plan_id: el.dataset.plan, custom_id: @json($tenant->slug) });
            },
            onApprove: function (data) {
                status.textContent = @js(__('ui.confirming_subscription'));
                return fetch(@json(route('admin.billing.activate')), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ subscription_id: data.subscriptionID, plan_key: el.dataset.key })
                }).then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); }).then(function (res) {
                    if (res.ok) { window.location = @json(route('admin.billing')) + '?activated=1'; }
                    else { status.textContent = res.j.message || @js(__('ui.something_broke_refresh')); }
                });
            },
            onError: function () { status.textContent = @js(__('ui.paypal_error_retry')); }
        }).render('#' + id);
    });
})();
</script>
@endpush
@endif
