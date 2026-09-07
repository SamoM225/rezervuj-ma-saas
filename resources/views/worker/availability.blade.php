@extends('layouts.admin')

@section('header_title', __('ui.moja_dostupnost'))

@section('content')
<div class="page-head">
    <div>
        <h2 class="page-title">{{ __('ui.moja_dostupnost') }}</h2>
        <p class="page-sub">{{ __('ui.pracovne_hodiny_pocas_ktorych_si_vas') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('worker.calendar') }}" class="btn btn-secondary">{{ __('ui.otvorit_kalendar') }}</a>
    </div>
</div>

@include('admin.workers._availability', [
    'worker' => $worker,
    'availabilities' => $availabilities,
    'storeUrl' => route('worker.availability.store'),
    'updateUrlTemplate' => route('worker.availability.update', ['availability' => '__ID__']),
    'destroyUrlTemplate' => route('worker.availability.destroy', ['availability' => '__ID__']),
    'forSelf' => true,
])
@endsection
