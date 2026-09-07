@extends('layouts.site')
@php use App\Support\Locales; @endphp
@section('title', __("site.legal.docs.{$doc}").' · rezervuj-ma.online')
@section('description', __('site.legal.lead'))
@section('og_type', 'article')

@section('content')
<section class="wrap band split">
    <nav class="toc" aria-label="{{ __('site.legal.title') }}">
        <a href="{{ Locales::route('legal.index') }}" class="mute">{{ __('site.legal.title') }}</a>
        @foreach ($docs as $item)
            <a href="{{ Locales::route('legal.show', ['doc' => $item]) }}" @if($item === $doc) aria-current="page" @endif>{{ __("site.legal.docs.{$item}") }}</a>
        @endforeach
    </nav>
    <article class="doc">
        @include($body, compact('version', 'effective', 'operator', 'siteUrl', 'emailProvider', 'links'))
    </article>
</section>
@endsection
