@extends('layouts.site')
@php use App\Http\Controllers\Platform\DirectoryController; use App\Support\Locales; @endphp
@section('title', __('site.directory.title').' · rezervuj-ma.online')
@section('description', __('site.directory.lead', ['count' => $total]))
@section('canonical', Locales::route('directory.index'))

@section('content')
<section class="wrap band">
    <h1 class="h2">{{ __('site.directory.title') }}</h1>
    <p class="lead soft" style="margin:.8rem 0 2.5rem">{{ __('site.directory.lead', ['count' => $total]) }}</p>

    @if ($categories->isEmpty())
        <p class="biz">{{ __('site.directory.empty') }}</p>
    @else
        <div class="list">
            @foreach ($categories as $category)
                <div class="biz" id="{{ $category['key'] }}">
                    <h2 class="h3"><a href="{{ DirectoryController::categoryUrl($category['key']) }}">{{ $category['label'] }}</a></h2>
                    <span class="meta">{{ __('site.directory.count', ['count' => $category['count']]) }}</span>
                    @if ($category['cities'])
                        <div class="chips" style="margin-top:.4rem">
                            @foreach ($category['cities'] as $city)
                                <a href="{{ DirectoryController::cityUrl($category['key'], $city['name']) }}">{{ $city['name'] }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</section>
@endsection
