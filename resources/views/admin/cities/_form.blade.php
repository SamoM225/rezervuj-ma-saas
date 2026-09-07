@php
    /** @var \App\Models\City|null $city */
    $city = $city ?? null;
    $selectedCategories = collect(old('categories', $city?->categories?->pluck('id')->all() ?? []))->map(fn ($id) => (int) $id)->all();
    $selectedWorkers = collect(old('workers', $city?->workers?->pluck('id')->all() ?? []))->map(fn ($id) => (int) $id)->all();
@endphp

<div class="card">
    <div class="form-grid">
        <div class="field">
            <label for="name">{{ __('ui.nazov') }}</label>
            <input class="input @error('name') is-invalid @enderror" type="text" id="name" name="name" value="{{ old('name', $city?->name) }}" required autocomplete="off" placeholder="{{ __('ui.napriklad_surany') }}">
            <p class="hint">{{ __('ui.kratky_nazov_ktory_zakaznik_vidi_pri') }}</p>
        </div>
        <div class="field">
            <label for="address">{{ __('ui.adresa') }}</label>
            <input class="input @error('address') is-invalid @enderror" type="text" id="address" name="address" value="{{ old('address', $city?->address) }}" autocomplete="off" placeholder="{{ __('ui.ulica_cislo_psc_mesto') }}">
            <p class="hint">{{ __('ui.zobrazi_sa_v_potvrdeni_a_prida') }}</p>
        </div>
    </div>
</div>

<div class="grid-2">
    <div class="card" style="margin:0">
        <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.kategorie_v_tejto_prevadzke') }}</h3>
        <p class="card-sub" style="margin-bottom:.9rem">{{ __('ui.zakaznik_uvidi_len_zaskrtnute_kategorie') }}</p>
        @if($categories->isEmpty())
            <p class="hint">{{ __('ui.najprv_vytvorte_kategorie') }}</p>
        @else
            <div class="flex flex-col gap-1">
                @foreach($categories as $category)
                    <label class="check">
                        <input type="checkbox" name="categories[]" value="{{ $category->id }}" @checked(in_array($category->id, $selectedCategories, true))>
                        <span>{{ $category->name }}</span>
                    </label>
                @endforeach
            </div>
        @endif
    </div>

    <div class="card" style="margin:0">
        <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.tim_v_tejto_prevadzke') }}</h3>
        <p class="card-sub" style="margin-bottom:.9rem">{{ __('ui.kazdy_odbornik_posobi_v_jednej_prevadzke') }}</p>
        @if($workers->isEmpty())
            <p class="hint">{{ __('ui.zatial_nikto_v_time') }}</p>
        @else
            <div class="flex flex-col gap-1">
                @foreach($workers as $worker)
                    <label class="check">
                        <input type="checkbox" name="workers[]" value="{{ $worker->id }}" @checked(in_array($worker->id, $selectedWorkers, true))>
                        <span>{{ $worker->name }}
                            @if($worker->city_id && $worker->city_id !== $city?->id)
                                <span class="hint" style="display:inline">· {{ __('ui.now_in') }} {{ $worker->city?->name ?? __('ui.other_location') }}</span>
                            @endif
                        </span>
                    </label>
                @endforeach
            </div>
        @endif
    </div>
</div>
