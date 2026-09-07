@extends('layouts.admin')

@section('header_title', __('ui.moj_kalendar'))

@section('content')
<div
    id="booking-calendar-app"
    data-user-id="{{ auth()->id() }}"
    data-user-role="{{ auth()->user()->role }}"
    data-can-manage-all="false"
    data-can-manage-blocks="true"
    data-can-update-bookings="true"
    data-can-delete-bookings="false"
    data-require-confirmation="{{ $requireConfirmation ? 'true' : 'false' }}"
    data-feed-url="{{ route('worker.calendar.feed') }}"
    data-booking-url="{{ route('worker.bookings.store') }}"
    data-block-store-url="{{ route('worker.calendar.blocks.store') }}"
    data-block-delete-url-template="{{ route('worker.calendar.blocks.destroy', ['block' => '__BLOCK__']) }}"
    data-block-update-url-template="{{ route('worker.calendar.blocks.update', ['block' => '__BLOCK__']) }}"
    data-status-url-template="{{ route('worker.bookings.update-status', ['booking' => '__BOOKING__']) }}"
    data-reschedule-url-template="{{ route('worker.bookings.reschedule', ['booking' => '__BOOKING__']) }}"
    data-slot-start="{{ $businessHours['start'] }}"
    data-slot-end="{{ $businessHours['end'] }}"
    data-workers="{{ $workers->toJson(JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
    data-services="{{ $services->toJson(JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
></div>
@endsection
