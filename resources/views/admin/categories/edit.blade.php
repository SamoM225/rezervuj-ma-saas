@extends('layouts.admin')

@section('header_title', $category->name)

@section('content')
<div style="max-width:40rem">
    <div class="page-head">
        <div>
            <h2 class="page-title">{{ $category->name }}</h2>
            <p class="page-sub">{{ __('ui.sluzby_patriace_do_kategorie_upravite_v') }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.categories.update', $category) }}">
        @csrf @method('PUT')
        <div class="card">
            <div class="flex flex-col gap-4">
                <div class="field">
                    <label for="name">{{ __('ui.nazov') }}</label>
                    <input class="input" type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required autocomplete="off">
                </div>
                <div class="field">
                    <label for="description">{{ __('ui.kratky_popis') }}</label>
                    <input class="input" type="text" id="description" name="description" value="{{ old('description', $category->description) }}" maxlength="255" placeholder="{{ __('ui.nepovinne_zobrazi_sa_pod_nazvom') }}">
                </div>
                <div class="field">
                    <span class="label">{{ __('ui.ponukat_v_prevadzkach') }}</span>
                    @if($cities->isEmpty())
                        <p class="hint">{{ __('ui.najprv_vytvorte_prevadzku') }}</p>
                    @else
                        <div class="flex flex-col gap-1">
                            @foreach($cities as $city)
                                <label class="check">
                                    <input type="checkbox" name="cities[]" value="{{ $city->id }}" @checked(in_array($city->id, old('cities', $category->cities->pluck('id')->all())))>
                                    <span>{{ $city->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="hint">{{ __('ui.kategoria_bez_prevadzky_sa_zakaznikom_nezobrazi') }}</p>
                    @endif
                </div>
            </div>
        </div>

        @if($category->services->isNotEmpty())
            <div class="card">
                <h3 class="card-title" style="margin-bottom:.5rem">{{ __('ui.sluzby_v_kategorii') }}</h3>
                <div class="flex flex-wrap gap-1">
                    @foreach($category->services as $service)
                        <a href="{{ route('admin.services.edit', $service) }}" class="chip" style="text-decoration:none">{{ $service->name }}</a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="form-actions">
            <a href="{{ route('admin.categories') }}" class="btn btn-ghost">{{ __('ui.spat_na_kategorie') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('ui.ulozit_zmeny') }}</button>
        </div>
    </form>
</div>
@endsection
