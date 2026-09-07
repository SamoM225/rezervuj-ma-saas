@php
    /** @var \App\Models\Service|null $service */
    $service = $service ?? null;
    $imageUrl = $service?->image_path ? $service->image_url : null;
@endphp

<div x-data="{ preview: @js($imageUrl), remove: false }">
    <div class="card">
        <div class="form-grid">
            <div class="field span-2">
                <label for="name">{{ __('ui.nazov_sluzby') }}</label>
                <input class="input @error('name') is-invalid @enderror" type="text" id="name" name="name" value="{{ old('name', $service?->name) }}" required autocomplete="off">
            </div>
            <div class="field">
                <label for="category_id">{{ __('ui.kategoria') }}</label>
                <select class="select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                    <option value="">{{ __('ui.vyberte_kategoriu') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) old('category_id', $service?->category_id) === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <p class="hint">{{ __('ui.zakaznik_najprv_vybera_kategoriu_potom_sluzbu') }}</p>
            </div>
            <div class="field">
                <label for="price">{{ __('ui.cena_v') }}</label>
                <input class="input @error('price') is-invalid @enderror" type="number" id="price" name="price" value="{{ old('price', $service?->price) }}" min="0" step="0.5" required inputmode="decimal">
            </div>
            <div class="field">
                <label for="duration">{{ __('ui.trvanie_v_minutach') }}</label>
                <input class="input @error('duration') is-invalid @enderror" type="number" id="duration" name="duration" value="{{ old('duration', $service?->duration ?? 60) }}" min="5" max="600" step="5" required>
                <p class="hint">{{ __('ui.dlzka_terminu_v_kalendari') }}</p>
            </div>
            <div class="field">
                <label for="break_time">{{ __('ui.pauza_po_sluzbe_v_minutach') }}</label>
                <input class="input @error('break_time') is-invalid @enderror" type="number" id="break_time" name="break_time" value="{{ old('break_time', $service?->break_time ?? 0) }}" min="0" max="180" step="5" required>
                <p class="hint">{{ __('ui.cas_na_pripravu_pred_dalsim_zakaznikom') }}</p>
            </div>
            <div class="field span-2">
                <label for="description">{{ __('ui.popis') }}</label>
                <textarea class="textarea" id="description" name="description" rows="3" maxlength="1000" placeholder="{{ __('ui.kratko_co_sluzba_zahrna_zobrazi_sa') }}">{{ old('description', $service?->description) }}</textarea>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.fotografia') }}</h3>
        <p class="card-sub" style="margin-bottom:1rem">{{ __('ui.nepovinna_zobrazi_sa_pri_sluzbe_v') }}</p>
        <div class="upload">
            <div class="upload-preview">
                <template x-if="preview && !remove"><img :src="preview" alt=""></template>
                <template x-if="!preview || remove">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" style="width:1.6rem;height:1.6rem"><rect x="3" y="5" width="18" height="14" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="m3 16 5-5 4 4 3-3 6 6"/><circle cx="16" cy="9" r="1.5"/></svg>
                </template>
            </div>
            <div style="flex:1">
                <input class="file" type="file" name="image" accept="image/*" @change="const f = $event.target.files[0]; if (f) { preview = URL.createObjectURL(f); remove = false; }">
                <p class="hint" style="margin-top:.35rem">{{ __('ui.jpg_png_alebo_webp_do_4') }}</p>
                @if($imageUrl)
                    <label class="check" style="margin-top:.5rem">
                        <input type="checkbox" name="remove_image" value="1" x-model="remove">
                        <span>{{ __('ui.odstranit_fotografiu') }}</span>
                    </label>
                @endif
            </div>
        </div>
    </div>
</div>
