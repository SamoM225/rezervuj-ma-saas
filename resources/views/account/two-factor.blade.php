@extends('layouts.admin')

@section('header_title', __('ui.zabezpecenie_uctu'))

@section('content')
<div style="max-width:44rem">
    <div class="page-head">
        <div>
            <h2 class="page-title">{{ __('ui.dvojfaktorove_overenie') }}</h2>
            <p class="page-sub">{{ __('ui.druha_vrstva_ochrany_prihlasenia_kod_generuje') }}</p>
        </div>
        @if($enabled)
            <span class="badge badge-ok">{{ __('ui.zapnute') }}</span>
        @else
            <span class="badge badge-muted">{{ __('ui.vypnute') }}</span>
        @endif
    </div>

    @if($recoveryCodes)
        <div class="card" style="border-color:#efd9a9;background:var(--warn-soft)">
            <h3 class="card-title">{{ __('ui.zalozne_kody') }}</h3>
            <p class="card-sub" style="margin-bottom:.9rem">{{ __('ui.ulozte_si_ich_na_bezpecne_miesto') }}</p>
            <div class="code-list">
                @foreach($recoveryCodes as $code)
                    <span>{{ $code }}</span>
                @endforeach
            </div>
        </div>
    @endif

    <div class="card">
        @if($enabled)
            <h3 class="card-title">{{ __('ui.vypnut_dvojfaktorove_overenie') }}</h3>
            <p class="card-sub" style="margin-bottom:1rem">{{ __('ui.prihlasenie_bude_chranene_len_heslom_pre') }}</p>
            <form method="POST" action="{{ route('account.2fa.disable') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                @csrf
                <div class="field" style="flex:1">
                    <label for="password">{{ __('ui.heslo') }}</label>
                    <input class="input" type="password" id="password" name="password" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn btn-danger">{{ __('ui.vypnut') }}</button>
            </form>

        @elseif($setupSecret)
            <h3 class="card-title">{{ __('ui.nastavenie_aplikacie') }}</h3>
            <p class="card-sub" style="margin-bottom:1.25rem">{{ __('ui.naskenujte_qr_kod_v_aplikacii_pripadne') }}</p>
            <div class="flex flex-col gap-6 sm:flex-row">
                <canvas id="qr" class="qr" aria-label="{{ __('ui.qr_kod_na_nastavenie_aplikacie') }}"></canvas>
                <div style="flex:1;min-width:0">
                    <p class="label" style="margin-bottom:.35rem">{{ __('ui.kluc_na_rucne_zadanie') }}</p>
                    <code class="mono" style="display:block;word-break:break-all;background:var(--paper);border:1px solid var(--line);border-radius:.6rem;padding:.6rem .75rem">{{ $setupSecret }}</code>

                    <form method="POST" action="{{ route('account.2fa.confirm') }}" style="margin-top:1.25rem">
                        @csrf
                        <div class="field">
                            <label for="code">{{ __('ui.kod_z_aplikacie') }}</label>
                            <div class="flex gap-2">
                                <input class="input mono" type="text" id="code" name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6" required style="max-width:10rem;text-align:center;letter-spacing:.25em;font-size:1.1rem">
                                <button type="submit" class="btn btn-primary">{{ __('ui.zapnut') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        @else
            <h3 class="card-title">{{ __('ui.zapnut_dvojfaktorove_overenie') }}</h3>
            <p class="card-sub" style="margin-bottom:1rem">{{ __('ui.po_zapnuti_bude_prihlasenie_vyzadovat_heslo') }}</p>
            <form method="POST" action="{{ route('account.2fa.enable') }}">
                @csrf
                <button type="submit" class="btn btn-primary">{{ __('ui.zacat_nastavenie') }}</button>
            </form>
        @endif
    </div>
</div>
@endsection

@if($setupSecret)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.QRCode) {
            QRCode.toCanvas(document.getElementById('qr'), @json($otpauthUri), { width: 176, margin: 1, color: { dark: '#1f1a14' } }, function () {});
        }
    });
</script>
@endpush
@endif
