@extends('layouts.admin')

@section('header_title', __('ui.nova_prevadzka'))

@section('content')
<div style="max-width:52rem">
    <div class="page-head">
        <div>
            <h2 class="page-title">{{ __('ui.pridat_prevadzku') }}</h2>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.cities.store') }}">
        @csrf
        @include('admin.cities._form', ['city' => null])
        <div class="form-actions">
            <a href="{{ route('admin.cities.index') }}" class="btn btn-ghost">{{ __('ui.zrusit') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('ui.vytvorit_prevadzku') }}</button>
        </div>
    </form>
</div>
@endsection
