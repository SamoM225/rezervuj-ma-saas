@extends('layouts.admin')

@section('header_title', __('ui.novy_clen_timu'))

@section('content')
<div style="max-width:52rem">
    <div class="page-head">
        <div>
            <h2 class="page-title">{{ __('ui.pridat_clena_timu') }}</h2>
            <p class="page-sub">{{ __('ui.po_ulozeni_nastavte_odbornikovi_dostupnost_aby') }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.workers.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.workers._form', ['worker' => null])
        <div class="form-actions">
            <a href="{{ route('admin.workers') }}" class="btn btn-ghost">{{ __('ui.zrusit') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('ui.vytvorit_ucet') }}</button>
        </div>
    </form>
</div>
@endsection
