@extends('layouts.admin')

@section('header_title', __('ui.prehlad'))

@php
    $shouldShowPending = $requireConfirmation && $showPendingStatus;
    $today = \Carbon\Carbon::today();
    $statusBadge = fn (string $status) => match ($status) {
        'pending' => ['badge-warn', __('ui.status_pending')],
        'confirmed' => ['badge-ok', __('ui.status_confirmed')],
        'completed' => ['badge-muted', __('ui.status_completed')],
        'cancelled' => ['badge-bad', __('ui.status_cancelled')],
        default => ['badge-muted', $status],
    };
    $dayLabel = function ($date) use ($today) {
        $d = \Carbon\Carbon::parse($date);
        if ($d->isSameDay($today)) return 'Dnes';
        if ($d->isSameDay($today->copy()->addDay())) return 'Zajtra';
        return $d->locale('sk')->isoFormat('dd D. M.');
    };
@endphp

@section('header_actions')
    <a href="{{ route('admin.bookings.index') }}" class="btn btn-primary btn-sm" title="{{ __('ui.nova_rezervacia') }}" aria-label="{{ __('ui.nova_rezervacia') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
        <span>{{ __('ui.nova_rezervacia') }}</span>
    </a>
@endsection

@section('content')
<div class="page-head">
    <div>
        <h2 class="page-title">{{ $today->locale('sk')->isoFormat('dddd, D. MMMM YYYY') }}</h2>
        <p class="page-sub">
            {{ trans_choice('ui.today_bookings', $stats['today_bookings']) }}
            {{ __('ui.next_7_days', ['count' => $stats['upcoming_week_bookings']]) }}
        </p>
    </div>
</div>

<div class="grid-4" style="margin-bottom:1.25rem">
    <div class="stat is-accent">
        <p class="stat-label">{{ __('ui.dnes') }}</p>
        <p class="stat-value">{{ $stats['today_bookings'] }}</p>
        <p class="stat-note">{{ __('ui.rezervacii') }}</p>
    </div>
    <div class="stat">
        <p class="stat-label">{{ __('ui.tento_tyzden') }}</p>
        <p class="stat-value">{{ $stats['this_week_bookings'] }}</p>
        <p class="stat-note">{{ __('ui.od_pondelka_do_nedele') }}</p>
    </div>
    <div class="stat">
        <p class="stat-label">{{ __('ui.trzby_za_30_dni') }}</p>
        <p class="stat-value">{{ number_format((float) $stats['revenue_last_30'], 0, ',', ' ') }} €</p>
        <p class="stat-note">{{ __('ui.potvrdene_a_dokoncene') }}</p>
    </div>
    <div class="stat">
        <p class="stat-label">{{ $shouldShowPending ? __('ui.awaiting_confirmation') : __('ui.cancellation_rate') }}</p>
        <p class="stat-value">{{ $shouldShowPending ? $stats['pending_bookings'] : $stats['cancellation_rate'].' %' }}</p>
        <p class="stat-note">{{ $shouldShowPending ? __('ui.needs_your_reaction') : __('ui.last_30_days') }}</p>
    </div>
</div>

<div class="grid-2">
    <div class="card" style="margin:0">
        <div class="card-head">
            <div>
                <h3 class="card-title">{{ __('ui.najblizsie_terminy') }}</h3>
                <p class="card-sub">{{ __('ui.co_vas_caka_v_poradi') }}</p>
            </div>
            <a href="{{ route('admin.bookings.index') }}" class="link" style="font-size:.85rem">{{ __('ui.kalendar') }}</a>
        </div>
        @forelse($upcoming_bookings as $booking)
            @php [$badge, $label] = $statusBadge($booking->status); @endphp
            <div class="list-item">
                <span class="dot" style="background: {{ $booking->worker?->calendar_color ?? '#c19a3e' }}"></span>
                <div class="list-main">
                    <p class="list-title">{{ $booking->customer_name }}</p>
                    <p class="list-sub">{{ $booking->service?->name }} · {{ $booking->worker?->name }}</p>
                </div>
                <div class="list-aside">
                    <div style="font-weight:600;color:var(--ink)">{{ $dayLabel($booking->date) }}</div>
                    <div>{{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }}</div>
                </div>
                @if($booking->status !== 'confirmed')<span class="badge {{ $badge }}">{{ $label }}</span>@endif
            </div>
        @empty
            <div class="empty" style="padding:2rem 1rem">
                <h3>{{ __('ui.ziadne_nadchadzajuce_rezervacie') }}</h3>
                <p>{{ __('ui.nove_rezervacie_sa_zobrazia_tu_aj') }}</p>
            </div>
        @endforelse
    </div>

    <div class="card" style="margin:0">
        <div class="card-head">
            <div>
                <h3 class="card-title">{{ __('ui.rezervacie_za_14_dni') }}</h3>
                <p class="card-sub">{{ __('ui.pocet_terminov_podla_dna_bez_zrusenych') }}</p>
            </div>
        </div>
        <div style="position:relative;height:14rem"><canvas id="bookingChart" aria-label="{{ __('ui.graf_rezervacii_za_poslednych_14_dni') }}" role="img"></canvas></div>
    </div>
</div>

<div class="grid-2" style="margin-top:1.25rem">
    <div class="card" style="margin:0">
        <div class="card-head">
            <div>
                <h3 class="card-title">{{ __('ui.naposledy_vytvorene') }}</h3>
                <p class="card-sub">{{ __('ui.rezervacie_v_poradi_ako_prichadzali') }}</p>
            </div>
            <a href="{{ route('admin.customers.index') }}" class="link" style="font-size:.85rem">{{ __('ui.zakaznici') }}</a>
        </div>
        @forelse($recent_bookings as $booking)
            @php [$badge, $label] = $statusBadge($booking->status); @endphp
            <div class="list-item">
                <div class="list-main">
                    <p class="list-title">{{ $booking->customer_name }}</p>
                    <p class="list-sub">{{ $booking->service?->name }} · {{ \Carbon\Carbon::parse($booking->date)->format('j. n. Y') }} {{ substr($booking->start_time, 0, 5) }}</p>
                </div>
                <span class="badge {{ $badge }}">{{ $label }}</span>
            </div>
        @empty
            <div class="empty" style="padding:2rem 1rem"><h3>{{ __('ui.zatial_ziadne_rezervacie') }}</h3></div>
        @endforelse
    </div>

    <div class="card" style="margin:0">
        <div class="card-head">
            <div>
                <h3 class="card-title">{{ __('ui.vytazenost_timu') }}</h3>
                <p class="card-sub">{{ __('ui.rezervacie_za_poslednych_30_dni') }}</p>
            </div>
            <a href="{{ route('admin.workers') }}" class="link" style="font-size:.85rem">{{ __('ui.tim') }}</a>
        </div>
        @php $max = max(1, (int) $top_workers->max('bookings_count')); @endphp
        @forelse($top_workers as $worker)
            <div class="list-item" style="align-items:center">
                <span class="avatar" style="background:{{ $worker->calendar_color }}22;color:{{ $worker->calendar_color }}">{{ mb_strtoupper(mb_substr($worker->name, 0, 1)) }}</span>
                <div class="list-main">
                    <p class="list-title">{{ $worker->name }}</p>
                    <div style="height:.4rem;border-radius:999px;background:var(--line-2);margin-top:.4rem;overflow:hidden">
                        <div style="height:100%;width:{{ round($worker->bookings_count / $max * 100) }}%;background:{{ $worker->calendar_color }};border-radius:999px"></div>
                    </div>
                </div>
                <div class="list-aside" style="min-width:3rem"><strong style="color:var(--ink);font-size:1rem">{{ $worker->bookings_count }}</strong></div>
            </div>
        @empty
            <div class="empty" style="padding:2rem 1rem">
                <h3>{{ __('ui.zatial_nikto_v_time') }}</h3>
                <p>{{ __('ui.pridajte_prveho_odbornika_aby_si_zakaznici') }}</p>
                <a href="{{ route('admin.workers.create') }}" class="btn btn-primary btn-sm">{{ __('ui.pridat_do_timu') }}</a>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const canvas = document.getElementById('bookingChart');
        if (!canvas || !window.Chart) return;
        new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json(array_column($booking_chart_data, 'date')),
                datasets: [{
                    data: @json(array_column($booking_chart_data, 'bookings')),
                    backgroundColor: '#c19a3e',
                    hoverBackgroundColor: '#9c7a2b',
                    borderRadius: 6,
                    maxBarThickness: 28,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { displayColors: false, callbacks: { label: (ctx) => `${ctx.parsed.y} ${@js(__('ui.rezervacii'))}` } } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#857a6d', font: { family: 'Plus Jakarta Sans', size: 11 } } },
                    y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0, color: '#857a6d', font: { family: 'Plus Jakarta Sans', size: 11 } }, grid: { color: '#f1ece3' }, border: { display: false } },
                },
            },
        });
    })();
</script>
@endpush
