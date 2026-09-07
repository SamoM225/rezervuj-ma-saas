@extends('layouts.admin')

@section('header_title', $service->name)

@section('content')
<div style="max-width:48rem">
    <div class="page-head">
        <div>
            <h2 class="page-title">{{ $service->name }}</h2>
            <p class="page-sub">{{ __('ui.zmena_trvania_sa_prejavi_pri_novych') }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.services.update', $service) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('admin.services._form', ['service' => $service])
        <div class="form-actions">
            <a href="{{ route('admin.services') }}" class="btn btn-ghost">{{ __('ui.spat_na_sluzby') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('ui.ulozit_zmeny') }}</button>
        </div>
    </form>
</div>
@endsection
