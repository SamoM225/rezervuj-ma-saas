@php
    /**
     * Shared availability manager.
     * Expects: $worker, $availabilities, $storeUrl, $updateUrlTemplate (with __ID__), $destroyUrlTemplate (with __ID__), $forSelf (bool)
     */
    $dayNames = __('ui.day_short');
    $dayLong = __('ui.day_long');
    $rules = $availabilities->map(fn ($rule) => [
        'id' => $rule->id,
        'start_date' => optional($rule->start_date)->format('Y-m-d'),
        'end_date' => optional($rule->end_date)->format('Y-m-d'),
        'days_of_week' => array_map('intval', (array) $rule->days_of_week),
        'start_time' => \Carbon\Carbon::parse($rule->start_time)->format('H:i'),
        'end_time' => \Carbon\Carbon::parse($rule->end_time)->format('H:i'),
        'is_active' => (bool) $rule->is_active,
        'notes' => $rule->notes,
    ])->values();
    $today = now()->toDateString();
@endphp

<div x-data="availabilityManager({
        storeUrl: @js($storeUrl),
        updateUrl: @js($updateUrlTemplate),
        userId: @js($worker->id),
        rules: @js($rules),
    })">
    <div class="grid-2" style="align-items:start">
        <div>
            <div class="card" style="margin:0">
                <div class="card-head">
                    <div>
                        <h3 class="card-title">{{ __('ui.pracovne_hodiny') }}</h3>
                        <p class="card-sub">{{ __('ui.zakaznici_si_mozu_rezervovat_termin_len') }}</p>
                    </div>
                </div>

                @if($availabilities->isEmpty())
                    <div class="empty" style="padding:2rem 1rem">
                        <h3>{{ __('ui.zatial_ziadne_pracovne_hodiny') }}</h3>
                        <p>{{ $forSelf ? __('ui.no_rules_self') : __('ui.no_rules_other') }} {{ __('ui.add_first_rule_right') }}</p>
                    </div>
                @else
                    <div class="list">
                        @foreach($availabilities as $rule)
                            @php
                                $days = collect((array) $rule->days_of_week)->map(fn ($d) => (int) $d);
                                $ordered = collect([1, 2, 3, 4, 5, 6, 0])->filter(fn ($d) => $days->contains($d));
                                $expired = $rule->end_date && $rule->end_date->lt(now()->startOfDay());
                            @endphp
                            <div class="list-item" style="align-items:flex-start">
                                <div class="list-main">
                                    <p class="list-title tabular">{{ \Carbon\Carbon::parse($rule->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($rule->end_time)->format('H:i') }}</p>
                                    <div class="flex flex-wrap gap-1" style="margin:.35rem 0">
                                        @foreach([1, 2, 3, 4, 5, 6, 0] as $d)
                                            <span class="chip" style="{{ $days->contains($d) ? 'background:var(--gold-soft);border-color:transparent;color:var(--gold-ink);font-weight:600' : 'opacity:.4' }}">{{ $dayNames[$d] }}</span>
                                        @endforeach
                                    </div>
                                    <p class="list-sub">
                                        od {{ $rule->start_date->format('j. n. Y') }}
                                        {{ $rule->end_date ? 'do '.$rule->end_date->format('j. n. Y') : 'bez obmedzenia' }}
                                        @if($rule->notes) · {{ $rule->notes }} @endif
                                    </p>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    @if(!$rule->is_active)
                                        <span class="badge badge-muted">{{ __('ui.vypnute') }}</span>
                                    @elseif($expired)
                                        <span class="badge badge-warn">{{ __('ui.skoncilo') }}</span>
                                    @else
                                        <span class="badge badge-ok">{{ __('ui.aktivne') }}</span>
                                    @endif
                                    <div class="flex gap-1">
                                        <button type="button" class="btn btn-secondary btn-sm" @click="edit({{ $rule->id }})">{{ __('ui.upravit') }}</button>
                                        <form method="POST" action="{{ str_replace('__ID__', $rule->id, $destroyUrlTemplate) }}" onsubmit="return confirm({{ Js::from(__('ui.confirm_delete_rule')) }})">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--bad)">{{ __('ui.odstranit') }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="card" style="margin:0" id="availability-form">
            <div class="card-head">
                <div>
                    <h3 class="card-title" x-text="editingId ? {{ Js::from(__('ui.edit_rule')) }} : {{ Js::from(__('ui.new_rule')) }}"></h3>
                    <p class="card-sub">{{ __('ui.opakuje_sa_kazdy_tyzden_vo_vybranych') }}</p>
                </div>
                <button type="button" class="btn btn-ghost btn-sm" x-show="editingId" x-cloak @click="reset()">{{ __('ui.zrusit_upravu') }}</button>
            </div>

            <form method="POST" :action="editingId ? updateUrl.replace('__ID__', editingId) : storeUrl">
                @csrf
                <template x-if="editingId"><input type="hidden" name="_method" value="PUT"></template>
                <input type="hidden" name="user_id" :value="userId">

                <div class="field" style="margin-bottom:1rem">
                    <span class="label">{{ __('ui.dni_v_tyzdni') }}</span>
                    <div class="flex flex-wrap gap-2" role="group">
                        @foreach([1, 2, 3, 4, 5, 6, 0] as $d)
                            <label class="chip" style="cursor:pointer;padding:.45rem .7rem" :style="form.days.includes({{ $d }}) ? 'background:var(--gold);border-color:var(--gold);color:#fff;font-weight:600' : ''">
                                <input type="checkbox" name="days_of_week[]" value="{{ $d }}" class="sr-only" :checked="form.days.includes({{ $d }})" @change="toggleDay({{ $d }})">
                                {{ \Illuminate\Support\Str::ucfirst($dayLong[$d]) }}
                            </label>
                        @endforeach
                    </div>
                    @error('days_of_week')<p class="hint is-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-grid">
                    <div class="field">
                        <label for="av-start-time">{{ __('ui.od') }}</label>
                        <input class="input" type="time" id="av-start-time" name="start_time" x-model="form.start_time" required step="300">
                    </div>
                    <div class="field">
                        <label for="av-end-time">{{ __('ui.do') }}</label>
                        <input class="input" type="time" id="av-end-time" name="end_time" x-model="form.end_time" required step="300">
                    </div>
                    <div class="field">
                        <label for="av-start-date">{{ __('ui.plati_od') }}</label>
                        <input class="input" type="date" id="av-start-date" name="start_date" x-model="form.start_date" required>
                    </div>
                    <div class="field">
                        <label for="av-end-date">{{ __('ui.plati_do') }}</label>
                        <input class="input" type="date" id="av-end-date" name="end_date" x-model="form.end_date" :disabled="form.repeat" :min="form.start_date">
                        <label class="check" style="margin-top:.3rem">
                            <input type="checkbox" name="repeat_until_end_of_year" value="1" x-model="form.repeat">
                            <span>{{ __('ui.do_konca_roka') }}</span>
                        </label>
                    </div>
                    <div class="field span-2">
                        <label for="av-notes">{{ __('ui.poznamka') }}</label>
                        <input class="input" type="text" id="av-notes" name="notes" x-model="form.notes" placeholder="{{ __('ui.nepovinne_napriklad_len_objednani_pacienti') }}">
                    </div>
                </div>

                <label class="switch" style="margin-top:.75rem">
                    <span>{{ __('ui.pravidlo_je_aktivne') }}</span>
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" x-model="form.is_active">
                </label>

                <div class="form-actions" style="margin-top:.75rem;padding-top:1rem">
                    <button type="submit" class="btn btn-primary" x-text="editingId ? {{ Js::from(__('ui.ulozit_zmeny')) }} : {{ Js::from(__('ui.add_rule')) }}"></button>
                </div>
            </form>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('availabilityManager', ({ storeUrl, updateUrl, userId, rules }) => ({
            storeUrl, updateUrl, userId, rules,
            editingId: null,
            form: { days: [1, 2, 3, 4, 5], start_time: '09:00', end_time: '17:00', start_date: @json($today), end_date: '', repeat: false, notes: '', is_active: true },
            toggleDay(day) {
                this.form.days = this.form.days.includes(day) ? this.form.days.filter(d => d !== day) : [...this.form.days, day];
            },
            edit(id) {
                const rule = this.rules.find(r => r.id === id);
                if (!rule) return;
                this.editingId = id;
                this.form = { days: [...rule.days_of_week], start_time: rule.start_time, end_time: rule.end_time, start_date: rule.start_date, end_date: rule.end_date || '', repeat: false, notes: rule.notes || '', is_active: rule.is_active };
                document.getElementById('availability-form')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            },
            reset() {
                this.editingId = null;
                this.form = { days: [1, 2, 3, 4, 5], start_time: '09:00', end_time: '17:00', start_date: @json($today), end_date: '', repeat: false, notes: '', is_active: true };
            },
        }));
    });
</script>
@endpush
@endonce
