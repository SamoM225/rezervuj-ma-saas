@php
    /** @var \App\Models\BlacklistEntry|null $entry */
    $entry = $entry ?? null;
    $prefill = $prefill ?? [];
    $type = old('identifier_type', $entry?->identifier_type ?? (!empty($prefill['email']) ? 'email' : (!empty($prefill['phone']) ? 'phone' : 'email')));
    $value = old('identifier_value', $entry?->identifier_value ?? ($type === 'email' ? ($prefill['email'] ?? '') : ($prefill['phone'] ?? '')));
    $severity = old('severity', $entry?->severity ?? 'soft_ban');
    $severityHelp = [
        'warning' => __('ui.level_warning_desc'),
        'soft_ban' => __('ui.level_soft_ban_desc'),
        'hard_ban' => __('ui.level_hard_ban_desc'),
    ];
    $expires = old('expires_at', $entry?->expires_at?->format('Y-m-d\TH:i'));
@endphp

<div class="card" x-data="{ type: @js($type) }">
    @if($entry)
        <div class="kv" style="margin-bottom:1rem">
            <dt>{{ __('ui.blokovane') }}</dt><dd>{{ $entry->identifier_type === 'email' ? __('ui.e_mail') : __('ui.telefon') }} <strong>{{ $entry->identifier_value }}</strong></dd>
            <dt>{{ __('ui.pridane') }}</dt><dd>{{ $entry->created_at->format('j. n. Y H:i') }}{{ $entry->createdBy ? ' · '.$entry->createdBy->name : '' }}</dd>
            <dt>{{ __('ui.poruseni') }}</dt><dd>{{ $entry->violation_count }}{{ $entry->last_violation_at ? ', naposledy '.$entry->last_violation_at->format('j. n. Y') : '' }}</dd>
        </div>
        <div class="divider"></div>
    @else
        @if(!empty($prefill['name']))<p class="hint" style="margin-bottom:.75rem">{{ __('ui.zakaznik') }} <strong>{{ $prefill['name'] }}</strong></p>@endif
        <div class="form-grid" style="margin-bottom:1rem">
            <div class="field">
                <span class="label">{{ __('ui.co_blokovat') }}</span>
                <div class="flex gap-2">
                    <label class="chip" style="cursor:pointer;padding:.5rem .8rem" :style="type === 'email' ? 'background:var(--ink);color:#fff;border-color:var(--ink)' : ''">
                        <input type="radio" name="identifier_type" value="email" class="sr-only" x-model="type"> {{ __('ui.e_mail') }}
                    </label>
                    <label class="chip" style="cursor:pointer;padding:.5rem .8rem" :style="type === 'phone' ? 'background:var(--ink);color:#fff;border-color:var(--ink)' : ''">
                        <input type="radio" name="identifier_type" value="phone" class="sr-only" x-model="type"> {{ __('ui.telefon') }}
                    </label>
                </div>
            </div>
            <div class="field">
                <label for="identifier_value" x-text="type === 'email' ? @js(__('ui.email_address')) : @js(__('ui.phone_number'))"></label>
                <input class="input @error('identifier_value') is-invalid @enderror" :type="type === 'email' ? 'email' : 'tel'" id="identifier_value" name="identifier_value" value="{{ $value }}" required autocomplete="off">
                @error('identifier_value')<p class="hint is-error">{{ $message }}</p>@enderror
            </div>
        </div>
    @endif

    <div class="field" style="margin-bottom:1rem">
        <span class="label">{{ __('ui.uroven_blokovania') }}</span>
        <div class="flex flex-col gap-2">
            @foreach($severityOptions as $key => $label)
                <label class="check-group" style="cursor:pointer;display:flex;gap:.7rem;align-items:flex-start;padding:.8rem .9rem;{{ $severity === $key ? 'border-color:var(--gold);background:var(--gold-tint)' : '' }}">
                    <input type="radio" name="severity" value="{{ $key }}" @checked($severity === $key) style="margin-top:.2rem;accent-color:var(--gold)">
                    <span>
                        <span style="display:block;font-weight:600;font-size:.9rem">{{ $label }}</span>
                        <span class="hint">{{ $severityHelp[$key] }}</span>
                    </span>
                </label>
            @endforeach
        </div>
    </div>

    <div class="form-grid">
        <div class="field">
            <label for="reason_category">{{ __('ui.dovod') }}</label>
            <select class="select" id="reason_category" name="reason_category" required>
                @foreach($reasonCategoryOptions as $key => $label)
                    <option value="{{ $key }}" @selected(old('reason_category', $entry?->reason_category ?? 'no_show') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label for="expires_at">{{ __('ui.plati_do') }}</label>
            <input class="input" type="datetime-local" id="expires_at" name="expires_at" value="{{ $expires }}">
            <p class="hint">{{ __('ui.nechajte_prazdne_pre_blokovanie_bez_casoveho') }}</p>
        </div>
        <div class="field span-2">
            <label for="reason">{{ __('ui.poznamka_k_dovodu') }}</label>
            <textarea class="textarea" id="reason" name="reason" rows="2" maxlength="1000" style="min-height:3.5rem" placeholder="{{ __('ui.co_sa_stalo_zakaznik_tuto_poznamku') }}">{{ old('reason', $entry?->reason) }}</textarea>
        </div>
        <div class="field span-2">
            <label for="internal_notes">{{ __('ui.interne_poznamky') }}</label>
            <textarea class="textarea" id="internal_notes" name="internal_notes" rows="2" maxlength="2000" style="min-height:3.5rem" placeholder="{{ __('ui.nepovinne_len_pre_tim') }}">{{ old('internal_notes', $entry?->internal_notes) }}</textarea>
        </div>
    </div>

    <label class="switch" style="margin-top:.75rem">
        <span>{{ __('ui.blokovanie_je_aktivne') }}</span>
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $entry?->is_active ?? true))>
    </label>
</div>
