@extends('layouts.admin')

@section('header_title', __('ui.moje_rezervacie'))

@section('content')
<div class="card">
    <div class="empty">
        <h3>{{ __('ui.rezervacie_najdete_v_kalendari') }}</h3>
        <p>{{ __('ui.vsetky_vase_terminy_vratane_minulych_su') }}</p>
        <a href="{{ route('worker.calendar') }}" class="btn btn-primary btn-sm">{{ __('ui.otvorit_kalendar') }}</a>
    </div>
</div>
@endsection
