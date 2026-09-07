@extends('layouts.admin')

@section('header_title', __('ui.nastavenia_rezervacii'))

@php
    $v = fn (string $key, $default = null) => old($key, $settings[$key] ?? $default);
    $languages = ['sk' => 'Slovenčina', 'en' => 'English', 'cs' => 'Čeština'];
    $availableLanguages = collect(old('available_languages', $settings['available_languages'] ?? ['sk']))->all();
@endphp

@section('content')
@include('admin.settings._tabs')

<form method="POST" action="{{ route('admin.business-settings.update') }}">
    @csrf @method('PUT')

    <div class="grid-2" style="align-items:start">
        <div>
            <div class="card" style="margin:0">
                <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.otvaracie_hodiny_kalendara') }}</h3>
                <p class="card-sub" style="margin-bottom:1rem">{{ __('ui.rozsah_ktory_zobrazuje_kalendar_v_administracii') }}</p>
                <div class="form-grid">
                    <div class="field">
                        <label for="business_start_time">{{ __('ui.od') }}</label>
                        <input class="input" type="time" id="business_start_time" name="business_start_time" value="{{ $v('business_start_time', '08:00') }}" required>
                    </div>
                    <div class="field">
                        <label for="business_end_time">{{ __('ui.do') }}</label>
                        <input class="input" type="time" id="business_end_time" name="business_end_time" value="{{ $v('business_end_time', '18:00') }}" required>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.ako_daleko_dopredu') }}</h3>
                <div class="form-grid" style="margin-top:1rem">
                    <div class="field">
                        <label for="booking_advance_days">{{ __('ui.najviac_dni_dopredu') }}</label>
                        <input class="input" type="number" id="booking_advance_days" name="booking_advance_days" min="1" max="365" value="{{ $v('booking_advance_days', 60) }}" required>
                        <p class="hint">{{ __('ui.neskorsie_datumy_sa_v_kalendari_zakaznika') }}</p>
                    </div>
                    <div class="field">
                        <label for="booking_advance_hours">{{ __('ui.najskor_o_hodin') }}</label>
                        <input class="input" type="number" id="booking_advance_hours" name="booking_advance_hours" min="0" max="168" value="{{ $v('booking_advance_hours', 2) }}" required>
                        <p class="hint">{{ __('ui.minimalny_predstih_pred_zaciatkom_terminu') }}</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.terminy') }}</h3>
                <div class="form-grid" style="margin-top:1rem">
                    <div class="field">
                        <label for="service_buffer_minutes">{{ __('ui.pauza_medzi_terminmi_min') }}</label>
                        <input class="input" type="number" id="service_buffer_minutes" name="service_buffer_minutes" min="0" max="180" step="5" value="{{ $v('service_buffer_minutes', 0) }}" required>
                        <p class="hint">{{ __('ui.pouzije_sa_ak_sluzba_nema_vlastnu') }}</p>
                    </div>
                    <div class="field">
                        <label for="slot_hold_minutes">{{ __('ui.drzat_vybrany_cas_min') }}</label>
                        <input class="input" type="number" id="slot_hold_minutes" name="slot_hold_minutes" min="1" max="60" value="{{ $v('slot_hold_minutes', 5) }}" required>
                        <p class="hint">{{ __('ui.kym_zakaznik_vyplna_udaje_nikto_iny') }}</p>
                    </div>
                </div>
                <label class="switch" style="margin-top:.75rem">
                    <span>
                        {{ __('ui.len_pevne_zaciatky_terminov') }}
                        <p class="hint">{{ __('ui.online_sa_ponuknu_iba_casy_nadvazujuce') }}</p>
                    </span>
                    <input type="hidden" name="enforce_fixed_start_times" value="0">
                    <input type="checkbox" name="enforce_fixed_start_times" value="1" @checked($v('enforce_fixed_start_times', false))>
                </label>
            </div>

            <div class="card">
                <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.limity') }}</h3>
                <div class="form-grid" style="margin-top:1rem">
                    <div class="field">
                        <label for="max_daily_slots_per_customer">{{ __('ui.rezervacii_na_zakaznika_a_den') }}</label>
                        <input class="input" type="number" id="max_daily_slots_per_customer" name="max_daily_slots_per_customer" min="0" max="20" value="{{ $v('max_daily_slots_per_customer', 0) }}">
                        <p class="hint">{{ __('ui.znamena_bez_limitu') }}</p>
                    </div>
                    <div class="field">
                        <label for="max_bookings_per_worker_per_day">{{ __('ui.rezervacii_na_odbornika_a_den') }}</label>
                        <input class="input" type="number" id="max_bookings_per_worker_per_day" name="max_bookings_per_worker_per_day" min="0" max="100" value="{{ $v('max_bookings_per_worker_per_day', 0) }}">
                        <p class="hint">{{ __('ui.znamena_bez_limitu') }}</p>
                    </div>
                    <div class="field">
                        <label for="min_cancel_hours">{{ __('ui.zrusenie_online_najneskor_hodin_pred') }}</label>
                        <input class="input" type="number" id="min_cancel_hours" name="min_cancel_hours" min="0" max="240" value="{{ $v('min_cancel_hours', 0) }}">
                        <p class="hint">{{ __('ui.neskor_musi_zakaznik_zavolat_povoli_zrusenie') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="card" style="margin:0">
                <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.prijimanie_a_potvrdzovanie') }}</h3>
                <label class="switch">
                    <span>{{ __('ui.online_rezervacie_su_zapnute') }} <p class="hint">{{ __('ui.vypnutim_sa_rezervacna_stranka_zmeni_na') }}</p></span>
                    <input type="hidden" name="allow_online_booking" value="0">
                    <input type="checkbox" name="allow_online_booking" value="1" @checked($v('allow_online_booking', true))>
                </label>
                <label class="switch">
                    <span>{{ __('ui.nove_rezervacie_vyzaduju_potvrdenie') }} <p class="hint">{{ __('ui.termin_vznikne_ako_cakajuci_a_potvrdite') }}</p></span>
                    <input type="hidden" name="require_confirmation" value="0">
                    <input type="checkbox" name="require_confirmation" value="1" @checked($v('require_confirmation', false))>
                </label>
                <label class="switch">
                    <span>{{ __('ui.ukazovat_pocet_cakajucich_na_prehlade') }}</span>
                    <input type="hidden" name="show_pending_status" value="0">
                    <input type="checkbox" name="show_pending_status" value="1" @checked($v('show_pending_status', false))>
                </label>
            </div>

            <div class="card">
                <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.e_maily_zakaznikom') }}</h3>
                <label class="switch">
                    <span>{{ __('ui.posielat_potvrdenia_pripomienky_a_zrusenia') }} <p class="hint">{{ __('ui.vzhlad_a_texty_upravite_na_karte') }}</p></span>
                    <input type="hidden" name="send_email_notifications" value="0">
                    <input type="checkbox" name="send_email_notifications" value="1" @checked($v('send_email_notifications', true))>
                </label>
                <div class="field" style="margin-top:.75rem;max-width:16rem">
                    <label for="reminder_hours_before">{{ __('ui.pripomienka_pred_terminom_hodin') }}</label>
                    <input class="input" type="number" id="reminder_hours_before" name="reminder_hours_before" min="1" max="168" value="{{ $v('reminder_hours_before', 24) }}" required>
                </div>
            </div>

            <div class="card">
                <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.zatvorene_dni') }}</h3>
                <p class="card-sub" style="margin-bottom:.75rem">{{ __('ui.sviatky_a_dovolenky_celej_prevadzky_jeden') }}</p>
                <textarea class="textarea mono" name="business_holidays" rows="5" placeholder="2026-12-24&#10;2026-12-25&#10;2027-01-01">{{ old('business_holidays', implode("\n", (array) ($settings['business_holidays'] ?? []))) }}</textarea>
                <p class="hint" style="margin-top:.4rem">{{ __('ui.volno_jedneho_odbornika_rieste_blokovanim_casu') }}</p>
            </div>

            <div class="card">
                @php $multilingual = \App\Support\PlanLimits::currentAllows('multilingual_booking_page'); @endphp
                <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.jazyky_rezervacnej_stranky') }}</h3>
                @unless($multilingual) @include('admin._pro-lock', ['text' => __('admin.pro.languages')]) @endunless
                <p class="card-sub" style="margin-bottom:.75rem">{{ __('ui.administracia_je_v_slovencine_jazyky_sa') }}</p>
                <div class="form-grid">
                    <div class="field">
                        <label for="default_language">{{ __('ui.predvoleny_jazyk') }}</label>
                        <select class="select" id="default_language" name="default_language">
                            @foreach($languages as $code => $label)
                                <option value="{{ $code }}" @selected($v('default_language', 'sk') === $code)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <span class="label">{{ __('ui.dostupne_jazyky') }}</span>
                        <div class="flex flex-col gap-1">
                            @foreach($languages as $code => $label)
                                <label class="check"><input type="checkbox" name="available_languages[]" value="{{ $code }}" @disabled(! $multilingual) @checked(in_array($code, $availableLanguages, true))><span>{{ $label }}</span></label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <label class="switch" style="margin-top:.5rem">
                    <span>{{ __('ui.zobrazit_prepinac_jazykov') }} <p class="hint">{{ __('ui.zobrazi_sa_len_ak_je_dostupnych') }}</p></span>
                    <input type="hidden" name="language_switcher_enabled" value="0">
                    <input type="checkbox" name="language_switcher_enabled" value="1" @disabled(! $multilingual) @checked($v('language_switcher_enabled', false))>
                </label>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">{{ __('ui.ulozit_nastavenia') }}</button>
    </div>
</form>
@endsection
