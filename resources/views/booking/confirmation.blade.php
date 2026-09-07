@extends('layouts.app')

@section('title', __('customer.confirmation.title').' · '.\App\Models\BusinessSetting::get('business_name', config('app.name')))
@section('robots', 'noindex, nofollow')

@php
    $pending = $booking->status === 'pending';
    $cancelled = $booking->status === 'cancelled';
    $address = $booking->location?->address ?: ($booking->location?->name ?: $booking->city);
    $tenantSlug = \App\Support\Tenancy::current()?->slug;
@endphp

@section('content')
@include('partials.topbar', ['tagline' => __('customer.confirmation.tagline')])
<div class="bk-page">
    <div class="bk-shell">


        <div class="bk-status">
            <div class="bk-status-icon {{ $cancelled ? 'is-bad' : '' }}">
                @if($cancelled)
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
                @else
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                @endif
            </div>
            <h1>
                @if($cancelled) {{ __('customer.confirmation.cancelled_title') }}
                @elseif($pending) {{ __('customer.confirmation.pending_title') }}
                @else {{ __('customer.confirmation.confirmed_title') }}
                @endif
            </h1>
            <p class="lead">
                @if($cancelled) {{ __('customer.confirmation.cancelled_lead') }}
                @elseif($pending) {{ __('customer.confirmation.pending_lead') }}
                @else {{ __('customer.confirmation.confirmed_lead', ['email' => $booking->customer_email]) }}
                @endif
            </p>

            <dl class="bk-kv">
                <dt>{{ __('customer.confirmation.service') }}</dt><dd>{{ $booking->service?->name }}</dd>
                <dt>{{ __('customer.confirmation.worker') }}</dt><dd>{{ $booking->worker?->name }}</dd>
                <dt>{{ __('customer.confirmation.date') }}</dt><dd>{{ \Carbon\Carbon::parse($booking->date)->locale(app()->getLocale())->isoFormat('dddd D. MMMM YYYY') }}</dd>
                <dt>{{ __('customer.confirmation.time') }}</dt><dd class="tabular">{{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }}</dd>
                @if($address)<dt>{{ __('customer.confirmation.place') }}</dt><dd>{{ $address }}</dd>@endif
                @if(is_numeric($booking->service?->price))<dt>{{ __('customer.confirmation.price') }}</dt><dd class="tabular">{{ \App\Support\Money::format((float) $booking->service->price) }}</dd>@endif
            </dl>

            @if(!$cancelled && isset($calendarUrls))
                <p style="margin:1.25rem 0 .5rem;font-size:.85rem;color:var(--bk-muted)">{{ __('customer.confirmation.add_to_calendar') }}</p>
                <div class="bk-btn-row" style="margin-top:0">
                    <a class="bk-btn" href="{{ $calendarUrls['google'] }}" target="_blank" rel="noopener">Google</a>
                    <a class="bk-btn" href="{{ $calendarUrls['apple'] }}">{{ __('customer.confirmation.apple_ics') }}</a>
                    <a class="bk-btn" href="{{ $calendarUrls['outlook'] }}" target="_blank" rel="noopener">Outlook</a>
                </div>
            @endif

            <div class="bk-btn-row">
                <a class="bk-btn is-primary" href="{{ route('home', ['tenant' => $tenantSlug]) }}">{{ $cancelled ? __('customer.confirmation.pick_new') : __('customer.confirmation.back') }}</a>
                @if(!$cancelled)
                    <a class="bk-btn" href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('booking.cancel.show', now()->addDays(60), ['booking' => $booking->id]) }}">{{ __('customer.confirmation.cancel_booking') }}</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
