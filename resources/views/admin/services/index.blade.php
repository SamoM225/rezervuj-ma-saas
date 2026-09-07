@extends('layouts.admin')

@section('header_title', __('ui.sluzby'))

@section('content')
<div class="page-head">
    <div>
        <h2 class="page-title">{{ __('ui.sluzby') }}</h2>
        <p class="page-sub">{{ __('ui.cennik_ktory_si_zakaznici_vyberaju_pri') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
            {{ __('ui.pridat_sluzbu') }}
        </a>
    </div>
</div>

@if($services->isEmpty())
    <div class="card">
        <div class="empty">
            <h3>{{ __('ui.zatial_ziadne_sluzby') }}</h3>
            <p>{{ __('ui.pridajte_prvu_sluzbu_s_cenou_a') }}</p>
            <a href="{{ route('admin.services.create') }}" class="btn btn-primary btn-sm">{{ __('ui.pridat_sluzbu') }}</a>
        </div>
    </div>
@else
    <div x-data="{ q: '' }">
        <div class="toolbar">
            <label class="search">
                <span class="sr-only">{{ __('ui.hladat_sluzbu') }}</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                <input class="input input-sm" type="search" placeholder="{{ __('ui.hladat_sluzbu_alebo_kategoriu') }}" x-model="q" style="min-width:16rem">
            </label>
            <span class="hint">{{ __('ui.services_in_categories', ['services' => $services->count(), 'categories' => $services->pluck('category_id')->unique()->count()]) }}</span>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('ui.sluzba') }}</th>
                        <th>{{ __('ui.kategoria') }}</th>
                        <th class="is-num">{{ __('ui.trvanie') }}</th>
                        <th class="is-num">{{ __('ui.pauza') }}</th>
                        <th class="is-num">{{ __('ui.cena') }}</th>
                        <th style="text-align:right">{{ __('ui.akcie') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services->sortBy([['category.name', 'asc'], ['name', 'asc']]) as $service)
                        <tr x-show="!q || $el.dataset.search.includes(q.toLowerCase())" data-search="{{ mb_strtolower($service->name.' '.($service->category?->name ?? '')) }}">
                            <td>
                                <div class="flex items-center gap-3">
                                    @if($service->image_path)
                                        <img src="{{ $service->image_url }}" alt="" style="width:2.5rem;height:2.5rem;border-radius:.6rem;object-fit:cover;flex-shrink:0">
                                    @endif
                                    <div style="min-width:0">
                                        <div class="is-strong">{{ $service->name }}</div>
                                        @if($service->description)<div class="hint" style="max-width:36ch;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $service->description }}</div>@endif
                                    </div>
                                </div>
                            </td>
                            <td>{!! $service->category ? '<span class="chip">'.e($service->category->name).'</span>' : '<span class="badge badge-warn">'.e(__('ui.bez_kategorie')).'</span>' !!}</td>
                            <td class="is-num">{{ $service->duration }} min</td>
                            <td class="is-num">{{ $service->break_time ? $service->break_time.' min' : '–' }}</td>
                            <td class="is-num is-strong">{{ is_numeric($service->price) ? number_format((float) $service->price, 2, ',', ' ').' €' : $service->price }}</td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-secondary">{{ __('ui.upravit') }}</a>
                                    <form method="POST" action="{{ route('admin.services.destroy', $service) }}" onsubmit="return confirm({{ Js::from(__('ui.confirm_delete_service', ['name' => $service->name])) }})">
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
    </div>
@endif
@endsection
