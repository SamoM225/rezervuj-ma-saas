@extends('layouts.admin')

@section('header_title', __('ui.e_maily_zakaznikom'))

@section('content')
@include('admin.settings._tabs')
@php $pro = \App\Support\PlanLimits::currentAllows('custom_branding'); @endphp
@unless($pro) @include('admin._pro-lock', ['text' => __('admin.pro.email_templates')]) @endunless

<div x-data="emailTemplateEditor()" x-init="init()">
    <div class="page-head" style="margin-bottom:1rem">
        <div>
            <h2 class="page-title" style="font-size:1.2rem">{{ __('ui.vzhlad_a_texty_e_mailov') }}</h2>
            <p class="page-sub">{{ __('ui.jedna_sablona_pre_potvrdenie_pripomienku_cakanie') }}</p>
        </div>
        <div class="page-actions">
            <form method="POST" action="{{ route('admin.email-templates.reset') }}" onsubmit="return confirm({{ Js::from(__('ui.confirm_reset_email')) }})">
                @csrf
                <button type="submit" class="btn btn-ghost" @disabled(! $pro)>{{ __('ui.obnovit_predvolene') }}</button>
            </form>
            <form method="POST" action="{{ route('admin.email-templates.update') }}">
                @csrf @method('PUT')
                <input type="hidden" name="config" :value="JSON.stringify(config)">
                <button type="submit" class="btn btn-primary" @disabled(! $pro)>{{ __('ui.ulozit_sablonu') }}</button>
            </form>
        </div>
    </div>

    <div class="split split-wide-aside">
        <div class="flex flex-col gap-4">
            <div class="card" style="margin:0">
                <div class="field">
                    <label for="previewType">{{ __('ui.nahlad_pre') }}</label>
                    <select id="previewType" class="select" x-model="previewType" @change="refreshPreview()">
                        <template x-for="(label, key) in typeLabels" :key="key"><option :value="key" x-text="label"></option></template>
                    </select>
                </div>
            </div>

            <div class="card" style="margin:0">
                <h3 class="card-title" style="margin-bottom:.75rem">{{ __('ui.farby') }}</h3>
                <div class="grid-2" style="gap:.75rem">
                    <template x-for="(color, key) in config.colors" :key="key">
                        <div class="field">
                            <label x-text="colorLabels[key] || key"></label>
                            <div class="color-swatch">
                                <input type="color" :value="color" @input="config.colors[key] = $event.target.value; debouncedRefresh()">
                                <input type="text" class="input input-sm mono" :value="color" @input="config.colors[key] = $event.target.value; debouncedRefresh()" style="max-width:7rem">
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="card" style="margin:0">
                <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.casti_e_mailu') }}</h3>
                <p class="card-sub" style="margin-bottom:.5rem">{{ __('ui.vypnute_casti_sa_neposielaju_logo_a') }}</p>
                <template x-for="(block, key) in config.blocks" :key="key">
                    <div style="border-bottom:1px solid var(--line-2);padding:.4rem 0">
                        <label class="switch" style="border:0;padding:.3rem 0">
                            <span x-text="blockLabels[key] || key"></span>
                            <input type="checkbox" :checked="block.enabled" @change="block.enabled = $event.target.checked; debouncedRefresh()">
                        </label>
                        <template x-if="key === 'header' && block.enabled">
                            <label class="check" style="margin:.2rem 0 .4rem 0;font-size:.8rem">
                                <input type="checkbox" :checked="block.show_logo" @change="block.show_logo = $event.target.checked; debouncedRefresh()">
                                <span>{{ __('ui.zobrazit_logo_alebo_nazov_v_hlavicke') }}</span>
                            </label>
                        </template>
                        <template x-if="block.enabled && hasEditableFields(key)">
                            <div class="flex flex-col gap-2" style="padding:.2rem 0 .5rem">
                                <template x-for="(val, field) in editableFields(key, block)" :key="key + '-' + field">
                                    <div class="field">
                                        <label style="font-size:.75rem;color:var(--muted)" x-text="fieldLabel(field)"></label>
                                        <input type="text" class="input input-sm" :value="val" @input="block[field] = $event.target.value; debouncedRefresh()">
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <div class="card" style="margin:0">
                <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.texty_podla_typu') }}</h3>
                <p class="card-sub" style="margin-bottom:.75rem">{{ __('ui.znacky_ako') }} <code class="mono">@{{customer_name}}</code> {{ __('ui.sa_nahradia_skutocnymi_udajmi') }}</p>
                <template x-for="(texts, type) in config.texts" :key="'type-' + type">
                    <div style="padding:.6rem 0;border-bottom:1px solid var(--line-2)">
                        <p class="section-title" style="margin-bottom:.5rem" x-text="typeLabels[type] || type"></p>
                        <div class="flex flex-col gap-2">
                            <template x-for="(val, field) in texts" :key="type + '-' + field">
                                <div class="field">
                                    <label style="font-size:.75rem;color:var(--muted)" x-text="fieldLabel(field)"></label>
                                    <input type="text" class="input input-sm" :value="val" @input="config.texts[type][field] = $event.target.value; debouncedRefresh()">
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
                <details style="margin-top:.75rem">
                    <summary class="link" style="cursor:pointer;font-size:.8rem">{{ __('ui.dostupne_znacky') }}</summary>
                    <div class="flex flex-wrap gap-1" style="margin-top:.5rem">
                        @foreach(['customer_name', 'business_name', 'service_name', 'worker_name', 'booking_date', 'booking_start_time', 'booking_end_time', 'booking_time_range', 'booking_price', 'business_address', 'support_email', 'support_phone', 'reminder_time_text'] as $token)
                            @php $placeholder = '{{'.$token.'}}'; @endphp
                            <code class="chip mono" style="cursor:copy" onclick="navigator.clipboard?.writeText({{ json_encode($placeholder) }})" title="{{ __('ui.kliknutim_skopirujete') }}">{{ $placeholder }}</code>
                        @endforeach
                    </div>
                </details>
            </div>
        </div>

        <div style="position:sticky;top:4.75rem">
            <div class="card" style="margin:0;padding:.75rem">
                <div class="flex items-center justify-between" style="padding:.25rem .5rem .75rem">
                    <span class="section-title" style="margin:0">{{ __('ui.nahlad') }}</span>
                    <span class="hint" x-text="typeLabels[previewType] || previewType"></span>
                </div>
                <iframe id="previewFrame" title="{{ __('ui.nahlad_e_mailu') }}" style="width:100%;height:46rem;border:1px solid var(--line);border-radius:.75rem;background:#f4f6f8" srcdoc="<p style='font-family:sans-serif;text-align:center;padding:40px;color:#999'>{{ __('ui.nacitavam_nahlad') }}</p>"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function emailTemplateEditor() {
        return {
            config: @json($config),
            previewType: 'confirmed',
            debounceTimer: null,
            colorLabels: @js(__('ui.et_colors')),
            blockLabels: @js(__('ui.et_blocks')),
            typeLabels: @js(__('ui.et_types')),
            init() { this.refreshPreview(); },
            hasEditableFields(key) {
                const block = this.config.blocks[key];
                return block && Object.entries(block).some(([f, v]) => f !== 'enabled' && typeof v !== 'boolean');
            },
            editableFields(key, block) {
                return Object.fromEntries(Object.entries(block).filter(([f, v]) => f !== 'enabled' && typeof v !== 'boolean'));
            },
            fieldLabel(field) {
                return (@js(__('ui.et_fields')))[field] || field;
            },
            debouncedRefresh() {
                clearTimeout(this.debounceTimer);
                this.debounceTimer = setTimeout(() => this.refreshPreview(), 350);
            },
            refreshPreview() {
                const params = new URLSearchParams({ config: JSON.stringify(this.config), type: this.previewType });
                fetch(@json(route('admin.email-templates.preview')) + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.text())
                    .then(html => { document.getElementById('previewFrame').srcdoc = html; })
                    .catch(err => console.error('Preview error:', err));
            },
        };
    }
</script>
@endpush
