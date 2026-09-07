@extends('layouts.admin')

@section('header_title', __('ui.blokovani_zakaznici'))

@php
    $severityBadge = fn (string $severity) => match ($severity) {
        'hard_ban' => 'badge-bad',
        'soft_ban' => 'badge-warn',
        default => 'badge-info',
    };
    $hasFilters = collect($filters)->filter()->isNotEmpty();
@endphp

@section('content')
<div class="page-head">
    <div>
        <h2 class="page-title">{{ __('ui.blokovani_zakaznici') }}</h2>
        <p class="page-sub">{{ __('ui.e_maily_a_telefonne_cisla_ktore') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.blacklist.create') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
            {{ __('ui.blokovat_zakaznika') }}
        </a>
    </div>
</div>

<div class="grid-3" style="margin-bottom:1.25rem">
    <div class="stat"><p class="stat-label">{{ __('ui.aktivne_blokovania') }}</p><p class="stat-value">{{ $stats['active'] }}</p><p class="stat-note">{{ __('ui.of_records', ['total' => $stats['total']]) }}</p></div>
    <div class="stat"><p class="stat-label">{{ __('ui.uplne_blokovani') }}</p><p class="stat-value">{{ $stats['hard_bans'] }}</p></div>
    <div class="stat"><p class="stat-label">{{ __('ui.konci_do_7_dni') }}</p><p class="stat-value">{{ $stats['expiring_soon'] }}</p></div>
</div>

<form method="GET" action="{{ route('admin.blacklist.index') }}" class="toolbar">
    <label class="search">
        <span class="sr-only">{{ __('ui.hladat') }}</span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
        <input class="input input-sm" type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="{{ __('ui.e_mail_telefon_alebo_dovod') }}">
    </label>
    <select class="select input-sm" name="status" onchange="this.form.submit()">
        <option value="">{{ __('ui.vsetky_stavy') }}</option>
        <option value="active" @selected(($filters['status'] ?? '') === 'active')>{{ __('ui.aktivne') }}</option>
        <option value="expired" @selected(($filters['status'] ?? '') === 'expired')>{{ __('ui.neaktivne_alebo_skoncene') }}</option>
    </select>
    <select class="select input-sm" name="severity" onchange="this.form.submit()">
        <option value="">{{ __('ui.vsetky_urovne') }}</option>
        @foreach($severityOptions as $value => $label)
            <option value="{{ $value }}" @selected(($filters['severity'] ?? '') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <select class="select input-sm" name="reason_category" onchange="this.form.submit()">
        <option value="">{{ __('ui.vsetky_dovody') }}</option>
        @foreach($reasonCategoryOptions as $value => $label)
            <option value="{{ $value }}" @selected(($filters['reason_category'] ?? '') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">{{ __('ui.filtrovat') }}</button>
    @if($hasFilters)<a href="{{ route('admin.blacklist.index') }}" class="btn btn-ghost btn-sm">{{ __('ui.zrusit_filtre') }}</a>@endif
</form>

@if($entries->isEmpty())
    <div class="card">
        <div class="empty">
            <h3>{{ $hasFilters ? __('ui.nothing_matches_filter') : __('ui.nobody_blocked') }}</h3>
            <p>{{ $hasFilters ? __('ui.try_other_criteria') : __('ui.block_from_here') }}</p>
        </div>
    </div>
@else
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('ui.zakaznik') }}</th>
                    <th>{{ __('ui.uroven') }}</th>
                    <th>{{ __('ui.dovod') }}</th>
                    <th>{{ __('ui.platnost') }}</th>
                    <th class="is-num">{{ __('ui.poruseni') }}</th>
                    <th style="text-align:right">{{ __('ui.akcie') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entries as $entry)
                    @php $active = $entry->isCurrentlyActive(); @endphp
                    <tr style="{{ $active ? '' : 'opacity:.6' }}">
                        <td>
                            <div class="is-strong">{{ $entry->identifier_value }}</div>
                            <div class="hint">{{ $entry->identifier_type === 'email' ? __('ui.e_mail') : __('ui.telefon') }} · {{ __('ui.added_on') }} {{ $entry->created_at->format('j. n. Y') }}{{ $entry->createdBy ? ' · '.$entry->createdBy->name : '' }}</div>
                        </td>
                        <td><span class="badge {{ $severityBadge($entry->severity) }}">{{ $entry->getSeverityLabel() }}</span></td>
                        <td>
                            <div>{{ $entry->getReasonCategoryLabel() }}</div>
                            @if($entry->reason)<div class="hint" style="max-width:30ch;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $entry->reason }}">{{ $entry->reason }}</div>@endif
                        </td>
                        <td>
                            @if(!$entry->is_active)
                                <span class="badge badge-muted">{{ __('ui.vypnute') }}</span>
                            @elseif($entry->expires_at && $entry->expires_at->isPast())
                                <span class="badge badge-muted">{{ __('ui.skoncilo') }} {{ $entry->expires_at->format('j. n. Y') }}</span>
                            @elseif($entry->expires_at)
                                do {{ $entry->expires_at->format('j. n. Y') }}
                            @else
                                Bez obmedzenia
                            @endif
                        </td>
                        <td class="is-num">{{ $entry->violation_count }}</td>
                        <td>
                            <div class="row-actions">
                                <form method="POST" action="{{ route('admin.blacklist.toggle-status', $entry) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary">{{ $entry->is_active ? __('ui.vypnut') : __('ui.zapnut') }}</button>
                                </form>
                                <a href="{{ route('admin.blacklist.edit', $entry) }}" class="btn btn-secondary">{{ __('ui.upravit') }}</a>
                                <form method="POST" action="{{ route('admin.blacklist.destroy', $entry) }}" onsubmit="return confirm({{ Js::from(__('ui.confirm_remove_block', ['name' => $entry->identifier_value])) }})">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-ghost" style="color:var(--bad)">{{ __('ui.odstranit') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($entries->hasPages())
            <div class="table-foot">
                <span>{{ $entries->firstItem() }}–{{ $entries->lastItem() }} z {{ $entries->total() }}</span>
                <div class="flex gap-2">
                    @if($entries->onFirstPage())<span class="btn btn-secondary btn-sm" aria-disabled="true">{{ __('ui.predchadzajuca') }}</span>@else<a class="btn btn-secondary btn-sm" href="{{ $entries->previousPageUrl() }}">{{ __('ui.predchadzajuca') }}</a>@endif
                    @if($entries->hasMorePages())<a class="btn btn-secondary btn-sm" href="{{ $entries->nextPageUrl() }}">{{ __('ui.dalsia') }}</a>@else<span class="btn btn-secondary btn-sm" aria-disabled="true">{{ __('ui.dalsia') }}</span>@endif
                </div>
            </div>
        @endif
    </div>
@endif
@endsection
