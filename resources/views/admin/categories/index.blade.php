@extends('layouts.admin')

@section('header_title', __('ui.kategorie'))

@section('content')
<div class="page-head">
    <div>
        <h2 class="page-title">{{ __('ui.kategorie') }}</h2>
        <p class="page-sub">{{ __('ui.prvy_krok_vyberu_pre_zakaznika_kategoria') }}</p>
    </div>
</div>

<div class="split split-form-first">
    <div>
        @if($categories->isEmpty())
            <div class="card" style="margin:0">
                <div class="empty">
                    <h3>{{ __('ui.zatial_ziadne_kategorie') }}</h3>
                    <p>{{ __('ui.vytvorte_prvu_kategoriu_vpravo_potom_do') }}</p>
                </div>
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('ui.kategoria') }}</th>
                            <th>{{ __('ui.prevadzky') }}</th>
                            <th class="is-num">{{ __('ui.sluzby') }}</th>
                            <th class="is-num">{{ __('ui.odbornici') }}</th>
                            <th style="text-align:right">{{ __('ui.akcie') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td>
                                    <div class="is-strong">{{ $category->name }}</div>
                                    @if($category->description)<div class="hint">{{ $category->description }}</div>@endif
                                </td>
                                <td>
                                    @if($category->cities->isEmpty())
                                        <span class="badge badge-warn">{{ __('ui.nikde_sa_nezobrazuje') }}</span>
                                    @else
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($category->cities as $city)<span class="chip">{{ $city->name }}</span>@endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="is-num">{{ $category->services_count }}</td>
                                <td class="is-num">{{ $category->users_count }}</td>
                                <td>
                                    <div class="row-actions">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-secondary">{{ __('ui.upravit') }}</a>
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm({{ Js::from(__('ui.confirm_delete_category', ['name' => $category->name, 'count' => $category->services_count])) }})">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-ghost" style="color:var(--bad)">{{ __('ui.vymazat') }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="card" style="margin:0">
        <h3 class="card-title" style="margin-bottom:.25rem">{{ __('ui.nova_kategoria') }}</h3>
        <p class="card-sub" style="margin-bottom:1rem">{{ __('ui.napriklad_kozmetika_alebo_esteticka_medicina') }}</p>
        <form method="POST" action="{{ route('admin.categories.store') }}" class="flex flex-col gap-4">
            @csrf
            <div class="field">
                <label for="name">{{ __('ui.nazov') }}</label>
                <input class="input" type="text" id="name" name="name" value="{{ old('name') }}" required autocomplete="off">
            </div>
            <div class="field">
                <label for="description">{{ __('ui.kratky_popis') }}</label>
                <input class="input" type="text" id="description" name="description" value="{{ old('description') }}" maxlength="255" placeholder="{{ __('ui.nepovinne_zobrazi_sa_pod_nazvom') }}">
            </div>
            @if($cities->isNotEmpty())
                <div class="field">
                    <span class="label">{{ __('ui.ponukat_v_prevadzkach') }}</span>
                    <div class="flex flex-col gap-1">
                        @foreach($cities as $city)
                            <label class="check">
                                <input type="checkbox" name="cities[]" value="{{ $city->id }}" @checked(in_array($city->id, old('cities', $cities->pluck('id')->all())))>
                                <span>{{ $city->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif
            <div>
                <button type="submit" class="btn btn-primary">{{ __('ui.vytvorit_kategoriu') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
