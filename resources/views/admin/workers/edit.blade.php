@extends('layouts.admin')

@section('header_title', $worker->name)

@section('content')
<div style="max-width:52rem">
    <div class="page-head">
        <div>
            <h2 class="page-title">{{ $worker->name }}</h2>
            <p class="page-sub">{{ __('ui.upravte_udaje_uctu_a_sluzby_ktore') }}</p>
        </div>
        <div class="page-actions">
            @if($worker->role === 'worker' && auth()->user()->is_super_admin)
                <a href="{{ route('admin.workers.availability', $worker) }}" class="btn btn-secondary">{{ __('ui.dostupnost') }}</a>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('admin.workers.update', $worker) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('admin.workers._form', ['worker' => $worker])
        <div class="form-actions">
            <a href="{{ route('admin.workers') }}" class="btn btn-ghost">{{ __('ui.spat_na_tim') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('ui.ulozit_zmeny') }}</button>
        </div>
    </form>
</div>
@endsection
