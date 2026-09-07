@extends('layouts.admin')

@section('header_title', $city->name)

@section('content')
<div style="max-width:52rem">
    <div class="page-head">
        <div>
            <h2 class="page-title">{{ $city->name }}</h2>
            <p class="page-sub">{{ $city->address ?: 'Bez adresy' }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.cities.update', $city) }}">
        @csrf @method('PUT')
        @include('admin.cities._form', ['city' => $city])
        <div class="form-actions">
            <a href="{{ route('admin.cities.index') }}" class="btn btn-ghost">{{ __('ui.spat_na_prevadzky') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('ui.ulozit_zmeny') }}</button>
        </div>
    </form>
</div>
@endsection
