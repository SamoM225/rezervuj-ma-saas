@extends('layouts.admin')

@section('header_title', __('ui.dostupnost').' · '.$worker->name)

@section('content')
<div class="page-head">
    <div>
        <h2 class="page-title">{{ __('ui.dostupnost') }}: {{ $worker->name }}</h2>
        <p class="page-sub">{{ __('ui.pracovne_hodiny_pocas_ktorych_si_zakaznici') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.workers.edit', $worker) }}" class="btn btn-secondary">{{ __('ui.upravit_profil') }}</a>
        <a href="{{ route('admin.workers') }}" class="btn btn-ghost">{{ __('ui.spat_na_tim') }}</a>
    </div>
</div>

@include('admin.workers._availability', [
    'worker' => $worker,
    'availabilities' => $availabilities,
    'storeUrl' => route('admin.workers.availability.store'),
    'updateUrlTemplate' => route('admin.workers.availability.update', ['availability' => '__ID__']),
    'destroyUrlTemplate' => route('admin.workers.availability.destroy', ['availability' => '__ID__']),
    'forSelf' => false,
])
@endsection
