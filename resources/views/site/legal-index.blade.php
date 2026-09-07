@extends('layouts.site')
@php use App\Support\Locales; @endphp
@section('title', __('site.legal.title').' · rezervuj-ma.online')
@section('description', __('site.legal.lead'))

@section('content')
<section class="wrap band">
    <div class="doc">
        <h1>{{ __('site.legal.title') }}</h1>
        <p class="soft lead">{{ __('site.legal.lead') }}</p>
        <ul style="margin-top:2rem;padding:0;list-style:none;display:grid;gap:.6rem">
            @foreach ($docs as $doc)
                <li class="biz" style="padding:1rem 1.2rem">
                    <a class="h3" href="{{ Locales::route('legal.show', ['doc' => $doc]) }}" style="text-decoration:none">{{ __("site.legal.docs.{$doc}") }}</a>
                    <span class="meta">{{ __('site.legal.version', ['version' => config("legal.versions.{$doc}", config('legal.versions.terms')), 'date' => '1. 10. 2026']) }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</section>
@endsection
