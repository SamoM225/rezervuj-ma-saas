@extends('layouts.admin')

@section('header_title', __('ui.moja_nastenka'))

@php
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

@section('content')
<div class="page-head">
    <div>
        <h2 class="page-title">{{ __('ui.greeting_name', ['name' => \Illuminate\Support\Str::of($worker->name)->explode(' ')->first()]) }}</h2>
        <p class="page-sub">
            {{ $today->locale('sk')->isoFormat('dddd, D. MMMM') }}.
            {{ trans_choice('ui.today_bookings', $stats['today_bookings']) }}
        </p>
    </div>
    <div class="page-actions">
        <a href="{{ route('worker.availability') }}" class="btn btn-secondary btn-sm">{{ __('ui.moja_dostupnost') }}</a>
        <a href="{{ route('worker.calendar') }}" class="btn btn-primary btn-sm">{{ __('ui.otvorit_kalendar') }}</a>
    </div>
</div>

<div class="grid-3" style="margin-bottom:1.25rem">
    <div class="stat is-accent">
        <p class="stat-label">{{ __('ui.dnes') }}</p>
        <p class="stat-value">{{ $stats['today_bookings'] }}</p>
        <p class="stat-note">{{ __('ui.rezervacii') }}</p>
    </div>
    <div class="stat">
        <p class="stat-label">{{ __('ui.nadchadzajuce') }}</p>
        <p class="stat-value">{{ $stats['upcoming_bookings'] }}</p>
        <p class="stat-note">{{ __('ui.od_dnesneho_dna') }}</p>
    </div>
    <div class="stat">
        <p class="stat-label">{{ $requireConfirmation ? __('ui.awaiting_confirmation') : __('ui.dokoncene') }}</p>
        <p class="stat-value">{{ $requireConfirmation ? $stats['pending_bookings'] : $stats['completed_bookings'] }}</p>
        <p class="stat-note">{{ $requireConfirmation ? __('ui.needs_reaction') : __('ui.whole_period') }}</p>
    </div>
</div>

<div class="grid-2">
    <div class="card" style="margin:0">
        <div class="card-head">
            <div>
                <h3 class="card-title">{{ __('ui.najblizsie_terminy') }}</h3>
                <p class="card-sub">{{ __('ui.vase_dalsie_rezervacie_v_poradi') }}</p>
            </div>
            <a href="{{ route('worker.calendar') }}" class="link" style="font-size:.85rem">{{ __('ui.kalendar') }}</a>
        </div>
        @forelse($upcomingBookings as $booking)
            @php [$badge, $label] = $statusBadge($booking->status); @endphp
            <div class="list-item">
                <div class="list-main">
                    <p class="list-title">{{ $booking->customer_name }}</p>
                    <p class="list-sub">{{ $booking->service?->name ?? __('ui.rezervacia') }}</p>
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
                <p>{{ __('ui.skontrolujte_ci_mate_nastavenu_dostupnost_aby') }}</p>
                <a href="{{ route('worker.availability') }}" class="btn btn-secondary btn-sm">{{ __('ui.nastavit_dostupnost') }}</a>
            </div>
        @endforelse
    </div>

    <div class="card" style="margin:0">
        <div class="card-head">
            <div>
                <h3 class="card-title">{{ __('ui.poslednych_7_dni') }}</h3>
                <p class="card-sub">{{ __('ui.pocet_vasich_rezervacii_podla_dna') }}</p>
            </div>
        </div>
        <div style="position:relative;height:12rem"><canvas id="workerChart" role="img" aria-label="{{ __('ui.graf_rezervacii_za_poslednych_7_dni') }}"></canvas></div>

        <div class="divider"></div>
        <h4 class="section-title">{{ __('ui.naposledy') }}</h4>
        @forelse($recentBookings->take(4) as $booking)
            @php [$badge, $label] = $statusBadge($booking->status); @endphp
            <div class="list-item" style="padding:.55rem 0">
                <div class="list-main">
                    <p class="list-title">{{ $booking->customer_name }}</p>
                    <p class="list-sub">{{ $booking->service?->name }} · {{ \Carbon\Carbon::parse($booking->date)->format('j. n.') }} {{ substr($booking->start_time, 0, 5) }}</p>
                </div>
                <span class="badge {{ $badge }}">{{ $label }}</span>
            </div>
        @empty
            <p class="hint">{{ __('ui.zatial_ziadne_rezervacie') }}</p>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const canvas = document.getElementById('workerChart');
        if (!canvas || !window.Chart) return;
        new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json(array_column($bookingChartData, 'date')),
                datasets: [{ data: @json(array_column($bookingChartData, 'bookings')), backgroundColor: @json($worker->calendar_color ?: '#c19a3e'), borderRadius: 6, maxBarThickness: 32 }],
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { displayColors: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#857a6d', font: { family: 'Plus Jakarta Sans', size: 11 } } },
                    y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0, color: '#857a6d', font: { family: 'Plus Jakarta Sans', size: 11 } }, grid: { color: '#f1ece3' }, border: { display: false } },
                },
            },
        });
    })();
</script>
@endpush
