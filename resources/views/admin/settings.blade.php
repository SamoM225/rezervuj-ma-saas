@extends('layouts.admin')

@section('header_title', __('ui.nastavenia'))

@php
    use Illuminate\Support\Facades\Storage;
    $logoUrl = !empty($settings['business_logo_path']) ? Storage::disk('public')->url($settings['business_logo_path']) : null;
    $heroUrl = !empty($settings['hero_background_image_path']) ? Storage::disk('public')->url($settings['hero_background_image_path']) : null;
    $v = fn (string $key, $default = '') => old($key, $settings[$key] ?? $default);
@endphp

@section('content')
@php $pro = \App\Support\PlanLimits::currentAllows('custom_branding'); $widget = \App\Support\PlanLimits::currentAllows('embed_widget'); @endphp
@include('admin.settings._tabs')

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data"
      x-data="{
        accent: @js($v('main_accent')), text: @js($v('text_color')), bg: @js($v('booking_bg_color')),
        logo: @js($logoUrl), removeLogo: false, logoWidth: @js((int) $v('logo_width', 180)),
        hero: @js($heroUrl), removeHero: false, overlay: @js($v('hero_overlay_color', '#1f1a14')), opacity: @js((float) $v('hero_overlay_opacity', 0.55)),
        rgba() { const h = this.overlay.replace('#',''); const n = parseInt(h.length === 3 ? h.split('').map(c => c + c).join('') : h, 16); return `rgba(${(n>>16)&255},${(n>>8)&255},${n&255},${this.opacity})`; }
      }">
    @csrf

    <div class="split split-settings split-hide-aside-mobile">
        <div>
            <div class="card" style="margin:0">
                <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.firma') }}</h3>
                <p class="card-sub" style="margin-bottom:1rem">{{ __('ui.zobrazuje_sa_na_rezervacnej_stranke_v') }}</p>
                <div class="form-grid">
                    <div class="field">
                        <label for="business_name">{{ __('ui.nazov') }}</label>
                        <input class="input" type="text" id="business_name" name="business_name" value="{{ $v('business_name', config('app.name')) }}" required>
                    </div>
                    <div class="field">
                        <label for="business_owner">{{ __('ui.prevadzkovatel_pravny_nazov') }}</label>
                        <input class="input" type="text" id="business_owner" name="business_owner" value="{{ $v('business_owner') }}" placeholder="{{ __('ui.napriklad_mudr_jana_novakova') }}">
                        <p class="hint">{{ __('ui.uvadza_sa_v_zasadach_ochrany_osobnych') }}</p>
                    </div>
                    <div class="field span-2">
                        <label for="business_registration">{{ __('ui.ico_dic_registracia') }}</label>
                        <input class="input" type="text" id="business_registration" name="business_registration" value="{{ $v('business_registration') }}" placeholder="{{ __('ui.ico_12_345_678_dic_1234567890') }}">
                        <p class="hint">{{ __('ui.zobrazi_sa_v_podmienkach_rezervacie_a') }}</p>
                    </div>
                    <div class="field">
                        <label for="support_phone">{{ __('ui.telefon') }}</label>
                        <input class="input" type="tel" id="support_phone" name="support_phone" value="{{ $v('support_phone') }}">
                    </div>
                    <div class="field">
                        <label for="support_email">{{ __('ui.e_mail') }}</label>
                        <input class="input" type="email" id="support_email" name="support_email" value="{{ $v('support_email') }}">
                        <p class="hint">{{ __('ui.sem_chodia_hlasenia_problemov_a_odpovede') }}</p>
                    </div>
                    <div class="field span-2">
                        <label for="business_address">{{ __('ui.adresa_ak_sa_lisi_od_prevadzok') }}</label>
                        <input class="input" type="text" id="business_address" name="business_address" value="{{ $v('business_address') }}" placeholder="{{ __('ui.nepovinne_adresy_prevadzok_sa_beru_z') }}">
                    </div>
                    <div class="field span-2">
                        <label for="opening_hours_text">{{ __('ui.otvaracie_hodiny') }}</label>
                        <textarea class="textarea" id="opening_hours_text" name="opening_hours_text" rows="3" style="min-height:4.5rem" placeholder="{{ __('ui.po_pi_9_00_18_00') }}">{{ $v('opening_hours_text') }}</textarea>
                        <p class="hint">{{ __('ui.volny_text_do_paticky_rezervacnej_stranky') }}</p>
                    </div>
                    <div class="field">
                        <label for="privacy_effective_from">{{ __('ui.zasady_ochrany_udajov_ucinne_od') }}</label>
                        <input class="input" type="text" id="privacy_effective_from" name="privacy_effective_from" value="{{ $v('privacy_effective_from') }}" placeholder="1. 9. 2026">
                    </div>
                </div>
            </div>

            <div class="card">
                <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.farby_rezervacnej_stranky') }}</h3>
                @unless($pro) @include('admin._pro-lock', ['text' => __('admin.pro.branding')]) @endunless
                <p class="card-sub" style="margin-bottom:1rem">{{ __('ui.hlavna_farba_sa_pouzije_na_tlacidla') }}</p>
                <div class="grid-3">
                    <div class="field">
                        <label for="main_accent">{{ __('ui.hlavna_farba') }}</label>
                        <div class="color-swatch">
                            <input type="color" id="main_accent" name="main_accent" @disabled(! $pro) x-model="accent">
                            <span class="mono" x-text="accent"></span>
                        </div>
                    </div>
                    <div class="field">
                        <label for="text_color">{{ __('ui.farba_textu') }}</label>
                        <div class="color-swatch">
                            <input type="color" id="text_color" name="text_color" @disabled(! $pro) x-model="text">
                            <span class="mono" x-text="text"></span>
                        </div>
                    </div>
                    <div class="field">
                        <label for="booking_bg_color">{{ __('ui.pozadie') }}</label>
                        <div class="color-swatch">
                            <input type="color" id="booking_bg_color" name="booking_bg_color" @disabled(! $pro) x-model="bg">
                            <span class="mono" x-text="bg"></span>
                        </div>
                    </div>
                </div>
                <button type="button" class="link" style="font-size:.8rem;margin-top:.75rem;background:none;border:0;cursor:pointer" @click="accent = '#c19a3e'; text = '#211b14'; bg = '#fbf7f0'">{{ __('ui.obnovit_zlatu_temu') }}</button>
            </div>

            <div class="card">
                <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.logo') }}</h3>
                @unless($pro) @include('admin._pro-lock', ['text' => __('admin.pro.branding')]) @endunless
                <p class="card-sub" style="margin-bottom:1rem">{{ __('ui.zobrazi_sa_v_hornej_liste_rezervacnej') }}</p>
                <div class="upload">
                    <div class="upload-preview" style="width:6rem">
                        <template x-if="logo && !removeLogo"><img :src="logo" alt="" style="object-fit:contain;padding:.4rem"></template>
                        <template x-if="!logo || removeLogo"><span style="font-weight:700;font-size:1.3rem;color:var(--gold-ink)">{{ mb_strtoupper(mb_substr($v('business_name', config('app.name')), 0, 1)) }}</span></template>
                    </div>
                    <div style="flex:1">
                        <input class="file" type="file" name="business_logo" @disabled(! $pro) accept="image/*" @change="const f = $event.target.files[0]; if (f) { logo = URL.createObjectURL(f); removeLogo = false; }">
                        <p class="hint" style="margin-top:.35rem">{{ __('ui.png_alebo_svg_s_priehladnym_pozadim') }}</p>
                        @if($logoUrl)
                            <label class="check" style="margin-top:.5rem"><input type="checkbox" name="remove_business_logo" @disabled(! $pro) value="1" x-model="removeLogo"><span>{{ __('ui.odstranit_logo') }}</span></label>
                        @endif
                    </div>
                </div>
                <div class="field" style="margin-top:1rem;max-width:22rem">
                    <label for="logo_width">{{ __('ui.sirka_loga_v_hornej_liste') }} <span class="tabular" x-text="logoWidth + ' px'"></span></label>
                    <input type="range" id="logo_width" name="logo_width" @disabled(! $pro) min="60" max="360" step="10" x-model.number="logoWidth" style="accent-color:var(--gold)">
                </div>
            </div>

            <div class="card">
                <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.fotografia_na_pozadi') }}</h3>
                @unless($pro) @include('admin._pro-lock', ['text' => __('admin.pro.branding')]) @endunless
                <p class="card-sub" style="margin-bottom:1rem">{{ __('ui.nepovinna_stlmi_sa_prekrytim_aby_text') }}</p>
                <div class="upload">
                    <div class="upload-preview" style="width:8rem;height:4.5rem">
                        <template x-if="hero && !removeHero"><img :src="hero" alt=""></template>
                        <template x-if="!hero || removeHero"><span class="hint">{{ __('ui.bez_fotky') }}</span></template>
                    </div>
                    <div style="flex:1">
                        <input class="file" type="file" name="hero_background_image" @disabled(! $pro) accept="image/*" @change="const f = $event.target.files[0]; if (f) { hero = URL.createObjectURL(f); removeHero = false; }">
                        <p class="hint" style="margin-top:.35rem">{{ __('ui.aspon_1920_1080_px_do_5') }}</p>
                        @if($heroUrl)
                            <label class="check" style="margin-top:.5rem"><input type="checkbox" name="remove_hero_background_image" @disabled(! $pro) value="1" x-model="removeHero"><span>{{ __('ui.odstranit_fotografiu') }}</span></label>
                        @endif
                    </div>
                </div>
                <div class="form-grid" style="margin-top:1rem">
                    <div class="field">
                        <label for="hero_overlay_color">{{ __('ui.farba_prekrytia') }}</label>
                        <div class="color-swatch"><input type="color" id="hero_overlay_color" name="hero_overlay_color" @disabled(! $pro) x-model="overlay"><span class="mono" x-text="overlay"></span></div>
                    </div>
                    <div class="field">
                        <label for="hero_overlay_opacity">{{ __('ui.sila_prekrytia') }} <span class="tabular" x-text="Math.round(opacity * 100) + ' %'"></span></label>
                        <input type="range" id="hero_overlay_opacity" name="hero_overlay_opacity" @disabled(! $pro) min="0" max="1" step="0.05" x-model.number="opacity" style="accent-color:var(--gold)">
                    </div>
                </div>
            </div>

            <div class="card">
                <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.fotografie_v_ponuke') }}</h3>
                <p class="card-sub" style="margin-bottom:.5rem">{{ __('ui.fotografie_sluzieb_a_clenov_timu_na') }}</p>
                <label class="switch">
                    <span>{{ __('ui.zobrazovat_fotografie_sluzieb') }}</span>
                    <input type="hidden" name="show_service_images" value="0">
                    <input type="checkbox" name="show_service_images" value="1" @checked($v('show_service_images', true))>
                </label>
                <label class="switch">
                    <span>{{ __('ui.zobrazovat_fotografie_odbornikov') }}</span>
                    <input type="hidden" name="show_worker_avatars" value="0">
                    <input type="checkbox" name="show_worker_avatars" value="1" @checked($v('show_worker_avatars', true))>
                </label>
            </div>
        </div>

        <div style="position:sticky;top:4.75rem">
            <div class="card" style="margin:0">
                <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.nahlad') }}</h3>
                <p class="card-sub" style="margin-bottom:1rem">{{ __('ui.priblizne_tak_vyzera_rezervacna_stranka_s') }}</p>
                <div style="border-radius:1rem;overflow:hidden;border:1px solid var(--line)">
                    <div :style="`background:${bg};color:${text};padding:1.1rem;font-family:var(--font);position:relative;min-height:16rem`">
                        <div x-show="hero && !removeHero" :style="`position:absolute;inset:0;background:linear-gradient(${rgba()}, ${rgba()}), url('${hero}') center/cover`"></div>
                        <div style="position:relative">
                            <div class="flex items-center gap-2" style="margin-bottom:1rem">
                                <template x-if="logo && !removeLogo"><img :src="logo" alt="" :style="`max-width:${Math.min(logoWidth, 160)}px;max-height:2.5rem`"></template>
                                <template x-if="!logo || removeLogo"><span :style="`width:2rem;height:2rem;border-radius:.6rem;display:grid;place-items:center;background:${accent};color:#fff;font-weight:700`">{{ mb_strtoupper(mb_substr($v('business_name', config('app.name')), 0, 1)) }}</span></template>
                                <strong style="font-size:.9rem" x-text="$refs.nameInput?.value || @js($v('business_name', config('app.name')))"></strong>
                            </div>
                            <div style="background:#fff;border-radius:.9rem;padding:.9rem;border:1px solid rgba(0,0,0,.06)">
                                <div class="flex items-center gap-2" style="margin-bottom:.7rem">
                                    <span :style="`width:1.5rem;height:1.5rem;border-radius:999px;background:${accent};color:#fff;display:grid;place-items:center;font-size:.7rem;font-weight:700`">1</span>
                                    <span style="font-weight:700;font-size:.85rem">{{ __('ui.vyberte_sluzbu') }}</span>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <div :style="`border:1.5px solid ${accent};border-radius:.7rem;padding:.6rem .75rem;font-size:.8rem;display:flex;justify-content:space-between;background:${accent}18`"><span style="font-weight:600">{{ __('ui.konzultacia') }}</span><span :style="`color:${accent};font-weight:700`">30,00 €</span></div>
                                    <div style="border:1.5px solid #e8e1d6;border-radius:.7rem;padding:.6rem .75rem;font-size:.8rem;display:flex;justify-content:space-between"><span style="font-weight:600">{{ __('ui.osetrenie_pleti') }}</span><span style="font-weight:700">45,00 €</span></div>
                                </div>
                                <button type="button" :style="`margin-top:.9rem;width:100%;padding:.6rem;border:0;border-radius:.7rem;background:${accent};color:#fff;font-weight:700;font-size:.8rem`">{{ __('ui.potvrdit_rezervaciu') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" x-ref="nameInput" :value="document.getElementById('business_name')?.value">

    <div class="card" style="margin-top:1.25rem">
        <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.widget_na_vasu_webstranku') }}</h3>
        @unless($widget) @include('admin._pro-lock', ['text' => __('admin.pro.widget')]) @endunless
        <p class="card-sub" style="margin-bottom:1rem">{{ __('ui.vlozte_tento_kod_na_svoju_stranku') }}</p>
        <div class="form-grid">
            <div class="field span-2">
                <label for="embed_allowed_origins">{{ __('ui.povolene_webstranky') }}</label>
                <input class="input" type="text" id="embed_allowed_origins" name="embed_allowed_origins" @disabled(! $widget) value="{{ $v('embed_allowed_origins') }}" placeholder="https://www.mojweb.sk, https://mojweb.sk">
                <p class="hint">{{ __('ui.cele_adresy_s_https_oddelene_ciarkou') }}</p>
            </div>
            <div class="field span-2">
                <label>{{ __('ui.kod_tlacidla') }}</label>
                <textarea class="textarea mono" readonly rows="3" style="min-height:4.5rem" onclick="this.select()">&lt;script src="{{ route('widget.script') }}" data-label="{{ __('widget.book_now') }}" data-mode="modal"&gt;&lt;/script&gt;</textarea>
                <p class="hint">{{ __('ui.volitelne_atributy') }} <code class="mono">data-label</code> {{ __('ui.text_tlacidla') }} <code class="mono">data-mode="tab"</code> {{ __('ui.otvori_rezervaciu_na_novej_karte') }} <code class="mono">data-target="#moje-tlacidlo"</code> {{ __('ui.napoji_rezervaciu_na_vlastne_tlacidlo_namiesto') }} <code class="mono">data-mode="inline" data-target="#rezervacia"</code> {{ __('ui.vlozi_formular_priamo_do_stranky') }}</p>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn btn-ghost">{{ __('ui.otvorit_rezervacnu_stranku') }}</a>
        <button type="submit" class="btn btn-primary">{{ __('ui.ulozit_nastavenia') }}</button>
    </div>
</form>

<div class="card" style="margin-top:1.25rem" x-data="{ confirmDelete: false }">
    <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.account') }}</h3>
    <p class="card-sub" style="margin-bottom:1rem">{{ __('ui.export_hint') }}</p>
    <div class="flex items-center gap-3" style="flex-wrap:wrap">
        <a href="{{ route('admin.account.export') }}" class="btn btn-secondary">{{ __('ui.export_all_data') }}</a>
        <button type="button" class="btn btn-ghost" style="color:var(--bad)" @click="confirmDelete = !confirmDelete">{{ __('ui.delete_account') }}</button>
    </div>
    <form method="POST" action="{{ route('admin.account.destroy') }}" x-show="confirmDelete" x-cloak style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--line, #e5e7eb)">
        @csrf @method('DELETE')
        <p class="hint" style="margin-bottom:.75rem">{{ __('ui.delete_account_hint') }}</p>
        @if($errors->has('password') || $errors->has('confirm_slug'))
            <div class="alert alert-bad"><div>{{ $errors->first('password') ?: $errors->first('confirm_slug') }}</div></div>
        @endif
        <div class="form-grid">
            <div class="field">
                <label for="confirm_slug">{{ __('ui.delete_account_confirm_label', ['slug' => \App\Support\Tenancy::current()?->slug]) }}</label>
                <input class="input" type="text" id="confirm_slug" name="confirm_slug" autocomplete="off" required>
            </div>
            <div class="field">
                <label for="delete_password">{{ __('ui.delete_account_password') }}</label>
                <input class="input" type="password" id="delete_password" name="password" autocomplete="current-password" required>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-danger">{{ __('ui.delete_account') }}</button>
        </div>
    </form>
</div>
@endsection
