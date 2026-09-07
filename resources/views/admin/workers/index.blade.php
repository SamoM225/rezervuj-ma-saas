@extends('layouts.admin')

@section('header_title', __('ui.tim'))

@php
    $roleBadge = fn (string $role) => match ($role) {
        'superadmin' => ['badge-gold', __('ui.role_superadmin')],
        'admin' => ['badge-info', __('ui.role_admin')],
        default => ['badge-muted', __('ui.role_specialist')],
    };
    $staff = $workers->sortBy(fn ($u) => [$u->role === 'worker' ? 0 : 1, $u->name]);
    $me = auth()->user();
@endphp

@section('content')
<div class="page-head">
    <div>
        <h2 class="page-title">{{ __('ui.tim') }}</h2>
        <p class="page-sub">{{ __('ui.odbornici_ktorych_si_zakaznici_rezervuju_a') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.workers.create') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
            {{ __('ui.pridat_clena_timu') }}
        </a>
    </div>
</div>

@if($staff->isEmpty())
    <div class="card">
        <div class="empty">
            <h3>{{ __('ui.zatial_nikto_v_time') }}</h3>
            <p>{{ __('ui.pridajte_prveho_odbornika_priradte_mu_sluzby') }}</p>
            <a href="{{ route('admin.workers.create') }}" class="btn btn-primary btn-sm">{{ __('ui.pridat_clena_timu') }}</a>
        </div>
    </div>
@else
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('ui.meno') }}</th>
                    <th>{{ __('ui.rola') }}</th>
                    <th>{{ __('ui.prevadzka') }}</th>
                    <th>{{ __('ui.kategorie') }}</th>
                    <th style="text-align:right">{{ __('ui.akcie') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staff as $user)
                    @php [$badge, $roleLabel] = $roleBadge($user->role); $canEdit = $user->role === 'worker' || $me->is_super_admin; @endphp
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <span class="avatar" style="background: {{ $user->calendar_color ?? '#c19a3e' }}22; color: {{ $user->calendar_color ?? '#9c7a2b' }}">
                                    @if($user->avatar_path)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($user->avatar_path) }}" alt="">
                                    @else
                                        {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                                    @endif
                                </span>
                                <div style="min-width:0">
                                    <div class="is-strong">{{ $user->name }}@if($user->is($me)) <span class="hint" style="display:inline">(vy)</span>@endif</div>
                                    <div class="hint">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge {{ $badge }} no-dot">{{ $roleLabel }}</span></td>
                        <td>{{ $user->city?->name ?? '–' }}</td>
                        <td>
                            @if($user->role !== 'worker')
                                <span class="hint">–</span>
                            @elseif($user->categories->isEmpty())
                                <span class="badge badge-warn">{{ __('ui.bez_sluzieb') }}</span>
                            @else
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->categories->take(3) as $category)
                                        <span class="chip">{{ $category->name }}</span>
                                    @endforeach
                                    @if($user->categories->count() > 3)
                                        <span class="chip chip-more" title="{{ $user->categories->slice(3)->pluck('name')->implode(', ') }}">+{{ $user->categories->count() - 3 }}</span>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="row-actions">
                                @if($user->role === 'worker' && $me->is_super_admin)
                                    <a href="{{ route('admin.workers.availability', $user) }}" class="btn btn-secondary">{{ __('ui.dostupnost') }}</a>
                                @endif
                                @if($canEdit)
                                    <a href="{{ route('admin.workers.edit', $user) }}" class="btn btn-secondary">{{ __('ui.upravit') }}</a>
                                    @unless($user->is($me))
                                        <form method="POST" action="{{ route('admin.workers.delete', $user) }}" onsubmit="return confirm({{ Js::from(__('ui.confirm_delete_user', ['name' => $user->name])) }})">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-ghost" style="color:var(--bad)">{{ __('ui.odstranit') }}</button>
                                        </form>
                                    @endunless
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
