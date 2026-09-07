@extends('layouts.admin')

@section('header_title', 'Blokovanie · '.$entry->identifier_value)

@section('content')
<div style="max-width:44rem">
    <div class="page-head">
        <div>
            <h2 class="page-title">{{ $entry->identifier_value }}</h2>
            <p class="page-sub">{{ __('ui.upravte_uroven_dovod_alebo_platnost_blokovania') }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.blacklist.update', $entry) }}">
        @csrf @method('PUT')
        @include('admin.blacklist._form', ['entry' => $entry])
        <div class="form-actions">
            <a href="{{ route('admin.blacklist.index') }}" class="btn btn-ghost">{{ __('ui.spat_na_zoznam') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('ui.ulozit_zmeny') }}</button>
        </div>
    </form>
</div>
@endsection
