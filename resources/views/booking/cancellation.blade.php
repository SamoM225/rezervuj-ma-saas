@extends('layouts.app')

@section('title', __('customer.cancellation.title').' · '.\App\Models\BusinessSetting::get('business_name', config('app.name')))
@section('robots', 'noindex, nofollow')

@php
    $booking->loadMissing(['service', 'worker']);
    $alreadyCancelled = $booking->status === 'cancelled' || session('success');
    $minimum = (int) \App\Models\BusinessSetting::get('min_cancel_hours', 0);
    $startsAt = \Carbon\Carbon::parse($booking->date.' '.$booking->start_time);
    $tooLate = $minimum > 0 && $startsAt->lt(now()->addHours($minimum));
    $supportPhone = \App\Models\BusinessSetting::get('support_phone');
    $homeUrl = route('home', ['tenant' => \App\Support\Tenancy::current()?->slug]);
@endphp

@section('content')
@include('partials.topbar', ['tagline' => __('customer.cancellation.title')])
<div class="bk-page">
    <div class="bk-shell">


        <div class="bk-status">
            @if($alreadyCancelled)
                <div class="bk-status-icon is-bad">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
                </div>
                <h1>{{ __('customer.cancellation.cancelled_title') }}</h1>
                <p class="lead">{{ __('customer.cancellation.cancelled_lead') }}</p>
                <div class="bk-btn-row">
                    <a class="bk-btn is-primary" href="{{ $homeUrl }}">{{ __('customer.confirmation.pick_new') }}</a>
                </div>
            @else
                <div class="bk-status-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 8v4l2.5 1.5"/></svg>
                </div>
                <h1>{{ __('customer.cancellation.ask_title') }}</h1>
                <p class="lead">{{ __('customer.cancellation.ask_lead') }}</p>

                <dl class="bk-kv">
                    <dt>{{ __('customer.confirmation.service') }}</dt><dd>{{ $booking->service?->name }}</dd>
                    <dt>{{ __('customer.confirmation.worker') }}</dt><dd>{{ $booking->worker?->name }}</dd>
                    <dt>{{ __('customer.cancellation.appointment') }}</dt><dd>{{ $startsAt->locale(app()->getLocale())->isoFormat('dddd D. MMMM YYYY') }}, <span class="tabular">{{ $startsAt->format('H:i') }}</span></dd>
                </dl>

                @if($errors->any())
                    <div class="bk-alert bk-alert-error" style="margin-top:1rem">{{ $errors->first() }}</div>
                @endif

                @if($tooLate)
                    <div class="bk-alert bk-alert-error" style="margin-top:1rem">
                        {{ __('customer.cancellation.too_late', ['hours' => $minimum]) }}
                        @if($supportPhone) {{ __('customer.cancellation.call_us') }} <a href="tel:{{ preg_replace('/\s+/', '', $supportPhone) }}" style="color:inherit;font-weight:700">{{ $supportPhone }}</a>. @endif
                    </div>
                    <div class="bk-btn-row"><a class="bk-btn" href="{{ $homeUrl }}">{{ __('customer.cancellation.back') }}</a></div>
                @else
                    <form method="POST" action="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('booking.cancel', now()->addHours(2), ['booking' => $booking->id]) }}" class="bk-btn-row">
                        @csrf
                        <button type="submit" class="bk-btn is-danger">{{ __('customer.cancellation.confirm') }}</button>
                        <a class="bk-btn" href="{{ $homeUrl }}">{{ __('customer.cancellation.keep') }}</a>
                    </form>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
