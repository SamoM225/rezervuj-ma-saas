@extends('layouts.admin')

@section('header_title', __('ui.kalendar'))

@section('content')
<div
    id="booking-calendar-app"
    data-user-id="{{ auth()->id() }}"
    data-user-role="{{ auth()->user()->role }}"
    data-can-manage-all="true"
    data-can-manage-blocks="true"
    data-can-update-bookings="true"
    data-can-delete-bookings="true"
    data-require-confirmation="{{ $requireConfirmation ? 'true' : 'false' }}"
    data-feed-url="{{ route('admin.bookings.calendar-data') }}"
    data-booking-url="{{ route('admin.bookings.store') }}"
    data-block-store-url="{{ route('admin.calendar.blocks.store') }}"
    data-block-delete-url-template="{{ route('admin.calendar.blocks.destroy', ['block' => '__BLOCK__']) }}"
    data-block-update-url-template="{{ route('admin.calendar.blocks.update', ['block' => '__BLOCK__']) }}"
    data-status-url-template="{{ route('admin.bookings.update-status', ['booking' => '__BOOKING__']) }}"
    data-reschedule-url-template="{{ route('admin.bookings.reschedule', ['booking' => '__BOOKING__']) }}"
    data-delete-url-template="{{ route('admin.bookings.destroy', ['booking' => '__BOOKING__']) }}"
    data-slot-start="{{ $businessHours['start'] }}"
    data-slot-end="{{ $businessHours['end'] }}"
    data-workers="{{ $workers->toJson(JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
    data-services="{{ $services->toJson(JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
></div>
@endsection
