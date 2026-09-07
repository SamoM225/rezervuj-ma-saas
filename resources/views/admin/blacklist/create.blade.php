@extends('layouts.admin')

@section('header_title', __('ui.blokovat_zakaznika'))

@section('content')
<div style="max-width:44rem">
    <div class="page-head">
        <div>
            <h2 class="page-title">{{ __('ui.blokovat_zakaznika') }}</h2>
            <p class="page-sub">{{ __('ui.blokuje_sa_e_mail_alebo_telefon') }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.blacklist.store') }}">
        @csrf
        @include('admin.blacklist._form', ['entry' => null, 'prefill' => $prefill])
        <div class="form-actions">
            <a href="{{ url()->previous() === url()->current() ? route('admin.blacklist.index') : url()->previous() }}" class="btn btn-ghost">{{ __('ui.zrusit') }}</a>
            <button type="submit" class="btn btn-danger">{{ __('ui.blokovat') }}</button>
        </div>
    </form>
</div>
@endsection
