@php
    /** @var \App\Models\User|null $worker */
    $worker = $worker ?? null;
    $me = auth()->user();
    $selectedServices = collect(old('services', $worker?->services?->pluck('id')->all() ?? []))->map(fn ($id) => (int) $id)->all();
    $currentColor = old('calendar_color', $worker?->calendar_color ?? $palette[0]);
    $currentRole = old('role', $worker?->role ?? 'worker');
    $avatarUrl = $worker?->avatar_path ? \Illuminate\Support\Facades\Storage::url($worker->avatar_path) : null;
@endphp

<div x-data="{ role: @js($currentRole), color: @js($currentColor), avatarPreview: @js($avatarUrl), removeAvatar: false }">
    <div class="card">
        <h3 class="card-title" style="margin-bottom:1rem">{{ __('ui.ucet') }}</h3>
        <div class="form-grid">
            <div class="field">
                <label for="name">{{ __('ui.meno_a_priezvisko') }}</label>
                <input class="input @error('name') is-invalid @enderror" type="text" id="name" name="name" value="{{ old('name', $worker?->name) }}" required autocomplete="off">
                <p class="hint">{{ __('ui.zobrazuje_sa_zakaznikom_pri_vybere_odbornika') }}</p>
            </div>
            <div class="field">
                <label for="email">{{ __('ui.e_mail') }}</label>
                <input class="input @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email', $worker?->email) }}" required autocomplete="off">
                <p class="hint">{{ __('ui.sluzi_na_prihlasenie') }}</p>
            </div>
            <div class="field">
                <label for="password">{{ $worker ? __('ui.new_password') : __('ui.heslo') }}</label>
                <input class="input @error('password') is-invalid @enderror" type="password" id="password" name="password" {{ $worker ? '' : 'required' }} minlength="12" autocomplete="new-password">
                <p class="hint">{{ $worker ? __('ui.leave_blank_password').' ' : '' }}{{ __('ui.min_12_chars') }}</p>
            </div>
            <div class="field">
                <label for="role">{{ __('ui.rola') }}</label>
                @if($me->is_super_admin)
                    <select class="select" id="role" name="role" x-model="role">
                        <option value="worker">{{ __('ui.odbornik_vlastny_kalendar_a_dostupnost') }}</option>
                        <option value="admin">{{ __('ui.administrator_sprava_rezervacii_a_ponuky') }}</option>
                        <option value="superadmin">{{ __('ui.spravca_vsetko_vratane_nastaveni') }}</option>
                    </select>
                @else
                    <input type="hidden" name="role" value="worker">
                    <input class="input" type="text" value="{{ __('ui.role_specialist') }}" readonly>
                @endif
            </div>
            <div class="field">
                <label for="city_id">{{ __('ui.prevadzka') }}</label>
                <select class="select" id="city_id" name="city_id">
                    <option value="">{{ __('ui.bez_prevadzky') }}</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" @selected((int) old('city_id', $worker?->city_id) === $city->id)>{{ $city->name }}</option>
                    @endforeach
                </select>
                <p class="hint">{{ __('ui.odbornik_sa_zakaznikom_ponukne_len_pre') }}</p>
            </div>
            <div class="field">
                <label for="calendar_color">{{ __('ui.farba_v_kalendari') }}</label>
                <div class="flex items-center gap-3">
                    <div class="palette" role="radiogroup" aria-label="{{ __('ui.farba_v_kalendari') }}">
                        @foreach($palette as $swatch)
                            <button type="button" :class="{ 'is-selected': color.toLowerCase() === '{{ strtolower($swatch) }}' }" style="background: {{ $swatch }}" @click="color = '{{ $swatch }}'" aria-label="{{ $swatch }}"></button>
                        @endforeach
                    </div>
                    <input type="color" id="calendar_color" name="calendar_color" x-model="color" style="width:2.2rem;height:2.2rem;padding:0;border:1px solid var(--line);border-radius:.5rem;background:transparent">
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.fotografia') }}</h3>
        <p class="card-sub" style="margin-bottom:1rem">{{ __('ui.zobrazuje_sa_zakaznikom_pri_vybere_odbornika') }}</p>
        <div class="upload">
            <div class="upload-preview">
                <template x-if="avatarPreview && !removeAvatar"><img :src="avatarPreview" alt=""></template>
                <template x-if="!avatarPreview || removeAvatar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" style="width:1.6rem;height:1.6rem"><path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg>
                </template>
            </div>
            <div style="flex:1">
                <input class="file" type="file" name="avatar" accept="image/*" @change="const f = $event.target.files[0]; if (f) { avatarPreview = URL.createObjectURL(f); removeAvatar = false; }">
                <p class="hint" style="margin-top:.35rem">{{ __('ui.jpg_png_alebo_webp_do_4') }}</p>
                @if($avatarUrl)
                    <label class="check" style="margin-top:.5rem">
                        <input type="checkbox" name="remove_avatar" value="1" x-model="removeAvatar">
                        <span>{{ __('ui.odstranit_fotografiu') }}</span>
                    </label>
                @endif
            </div>
        </div>
    </div>

    <div class="card" x-show="role === 'worker'" x-cloak>
        <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.sluzby_ktore_poskytuje') }}</h3>
        <p class="card-sub" style="margin-bottom:1rem">{{ __('ui.zakaznik_uvidi_tohto_odbornika_len_pri') }}</p>
        @if($categories->isEmpty())
            <div class="alert alert-warn" style="margin:0">{{ __('ui.najprv_vytvorte_kategorie_a_sluzby_v') }}</div>
        @else
            <div class="grid-2" style="gap:1rem">
                @foreach($categories as $category)
                    <div class="check-group" x-data="{ ids: @js($category->services->pluck('id')->map(fn ($id) => (string) $id)->all()) }">
                        <div class="flex items-center justify-between" style="margin-bottom:.5rem">
                            <p class="check-group-title" style="margin:0">{{ $category->name }}</p>
                            @if($category->services->isNotEmpty())
                                <button type="button" class="link" style="font-size:.75rem" @click="const boxes = $el.closest('.check-group').querySelectorAll('input[type=checkbox]'); const all = [...boxes].every(b => b.checked); boxes.forEach(b => b.checked = !all)">{{ __('ui.vsetky') }}</button>
                            @endif
                        </div>
                        @if($category->services->isEmpty())
                            <p class="hint">{{ __('ui.tato_kategoria_zatial_nema_sluzby') }}</p>
                        @else
                            <div class="flex flex-col gap-1">
                                @foreach($category->services as $service)
                                    <label class="check">
                                        <input type="checkbox" name="services[]" value="{{ $service->id }}" @checked(in_array($service->id, $selectedServices, true))>
                                        <span>{{ $service->name }} <span class="hint" style="display:inline">· {{ $service->duration }} min</span></span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
