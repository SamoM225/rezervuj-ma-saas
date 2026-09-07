@extends('layouts.admin')

@section('header_title', __('ui.nova_sluzba'))

@section('content')
<div style="max-width:48rem">
    <div class="page-head">
        <div>
            <h2 class="page-title">{{ __('ui.pridat_sluzbu') }}</h2>
            <p class="page-sub">{{ __('ui.po_ulozeni_nezabudnite_sluzbu_priradit_odbornikom') }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.services._form', ['service' => null])
        <div class="form-actions">
            <a href="{{ route('admin.services') }}" class="btn btn-ghost">{{ __('ui.zrusit') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('ui.vytvorit_sluzbu') }}</button>
        </div>
    </form>
</div>
@endsection
