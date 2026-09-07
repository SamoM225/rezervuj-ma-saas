@extends('layouts.app')

@section('title', $title.' · '.\App\Models\BusinessSetting::get('business_name', $tenant->name))
@section('robots', 'noindex, follow')

@push('styles')
<style>.bk-prose .legal-meta{color:var(--bk-muted);font-size:.9rem;margin-top:-.5rem}.bk-prose ul{padding-left:1.25rem}.bk-prose h2{margin-top:1.75rem}</style>
@endpush

@section('content')
@include('partials.topbar', ['tagline' => $title])
<div class="bk-page">
    <div class="bk-shell">
        <article class="bk-prose">
            @include($body)

            <div class="bk-btn-row">
                <a class="bk-btn is-primary" href="{{ route('home', ['tenant' => $tenant->slug]) }}">{{ __('customer.legal.book') }}</a>
                <a class="bk-btn" href="{{ $otherUrl }}">{{ $otherLabel }}</a>
            </div>
        </article>
    </div>
</div>
@endsection
