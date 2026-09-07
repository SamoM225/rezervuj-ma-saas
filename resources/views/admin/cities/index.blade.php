@extends('layouts.admin')

@section('header_title', __('ui.prevadzky'))

@section('content')
@php $canAddLocation = \App\Support\PlanLimits::currentCanAddLocation(); @endphp
<div class="page-head">
    <div>
        <h2 class="page-title">{{ __('ui.prevadzky') }}</h2>
        <p class="page-sub">{{ __('ui.miesta_kde_poskytujete_sluzby_ak_mate') }}</p>
    </div>
    <div class="page-actions">
        @if($canAddLocation)<a href="{{ route('admin.cities.create') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
            {{ __('ui.pridat_prevadzku') }}
        </a>@else @include('admin._pro-lock', ['text' => __('admin.pro.locations')]) @endif
    </div>
</div>

@if($cities->isEmpty())
    <div class="card">
        <div class="empty">
            <h3>{{ __('ui.zatial_ziadna_prevadzka') }}</h3>
            <p>{{ __('ui.pridajte_adresu_kde_zakaznikov_prijimate_zobrazi') }}</p>
            @if($canAddLocation)<a href="{{ route('admin.cities.create') }}" class="btn btn-primary btn-sm">{{ __('ui.pridat_prevadzku') }}</a>@else @include('admin._pro-lock', ['text' => __('admin.pro.locations')]) @endif
        </div>
    </div>
@else
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('ui.prevadzka') }}</th>
                    <th>{{ __('ui.kategorie') }}</th>
                    <th>{{ __('ui.tim') }}</th>
                    <th style="text-align:right">{{ __('ui.akcie') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cities as $city)
                    <tr>
                        <td>
                            <div class="is-strong">{{ $city->name }}</div>
                            <div class="hint">{{ $city->address ?: 'Bez adresy' }}</div>
                        </td>
                        <td>
                            @if($city->categories->isEmpty())
                                <span class="badge badge-warn">{{ __('ui.bez_kategorii') }}</span>
                            @else
                                <div class="flex flex-wrap gap-1">
                                    @foreach($city->categories as $category)<span class="chip">{{ $category->name }}</span>@endforeach
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($city->workers->isEmpty())
                                <span class="hint">{{ __('ui.nikto') }}</span>
                            @else
                                <div class="flex items-center gap-1">
                                    @foreach($city->workers->take(4) as $worker)
                                        <span class="avatar" style="width:1.8rem;height:1.8rem;font-size:.7rem;background:{{ $worker->calendar_color }}22;color:{{ $worker->calendar_color }}" title="{{ $worker->name }}">{{ mb_strtoupper(mb_substr($worker->name, 0, 1)) }}</span>
                                    @endforeach
                                    @if($city->workers->count() > 4)<span class="hint">+{{ $city->workers->count() - 4 }}</span>@endif
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="row-actions">
                                <a href="{{ route('admin.cities.edit', $city) }}" class="btn btn-secondary">{{ __('ui.upravit') }}</a>
                                <form method="POST" action="{{ route('admin.cities.destroy', $city) }}" onsubmit="return confirm({{ Js::from(__('ui.confirm_delete_city', ['name' => $city->name])) }})">
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
@endsection
