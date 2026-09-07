@extends('layouts.site')
@php
    use App\Http\Controllers\Platform\DirectoryController;
    use App\Support\Locales;
    $title = $city ? __('site.directory.meta_city', ['category' => $categoryLabel, 'city' => $city]) : __('site.directory.meta_category', ['category' => $categoryLabel]);
    $self = $city ? DirectoryController::cityUrl($categoryKey, $city) : DirectoryController::categoryUrl($categoryKey);
    $alternates = [];
    foreach (Locales::SUPPORTED as $l) { $alternates[$l] = $city ? DirectoryController::cityUrl($categoryKey, $city, $l) : DirectoryController::categoryUrl($categoryKey, $l); }
    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            ['@type' => 'BreadcrumbList', 'itemListElement' => array_values(array_filter([
                ['@type' => 'ListItem', 'position' => 1, 'name' => __('site.nav.directory'), 'item' => Locales::route('directory.index')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $categoryLabel, 'item' => DirectoryController::categoryUrl($categoryKey)],
                $city ? ['@type' => 'ListItem', 'position' => 3, 'name' => $city, 'item' => $self] : null,
            ]))],
            ['@type' => 'ItemList', 'itemListElement' => $tenants->values()->map(fn ($t, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'url' => url('/'.$t->slug), 'name' => $t->name])->all()],
        ],
    ];
@endphp
@section('title', $title.' · rezervuj-ma.online')
@section('description', __('site.directory.meta_desc', ['category' => mb_strtolower($categoryLabel)]))
@section('canonical', $self)
@push('head')<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>@endpush

@section('content')
<section class="wrap band">
    <p class="mute small"><a href="{{ Locales::route('directory.index') }}" style="text-decoration:none">{{ __('site.nav.directory') }}</a> › @if($city)<a href="{{ DirectoryController::categoryUrl($categoryKey) }}" style="text-decoration:none">{{ $categoryLabel }}</a> › {{ $city }}@else{{ $categoryLabel }}@endif</p>
    <h1 class="h2" style="margin-top:.5rem">{{ $city ? __('site.directory.in_city', ['category' => $categoryLabel, 'city' => $city]) : $categoryLabel }}</h1>
    <p class="soft" style="margin:.6rem 0 2rem">{{ __('site.directory.count', ['count' => $tenants->count()]) }}</p>

    @if ($cities->count() > 1)
        <div class="chips" style="margin-bottom:2rem">
            @foreach ($cities as $c)
                <a href="{{ DirectoryController::cityUrl($categoryKey, $c['name']) }}">{{ $c['name'] }}</a>
            @endforeach
        </div>
    @endif

    <div class="list">
        @foreach ($tenants as $t)
            <article class="biz">
                <h2 class="h3"><a href="{{ url('/'.$t->slug) }}">{{ $t->name }}</a></h2>
                <span class="meta">{{ $categoryLabel }}@if($t->city) · {{ $t->city }}@endif @if($t->address) · {{ $t->address }}@endif</span>
                @if ($t->description)<p class="soft small" style="margin:.2rem 0 0">{{ \Illuminate\Support\Str::limit($t->description, 160) }}</p>@endif
                <div style="display:flex;gap:.6rem;margin-top:.6rem;flex-wrap:wrap">
                    <a class="btn" style="padding:.55rem .95rem;font-size:.9rem" href="{{ url('/'.$t->slug.'/booking') }}">{{ __('site.directory.book') }}</a>
                    <a class="btn ghost" style="padding:.55rem .95rem;font-size:.9rem" href="{{ url('/'.$t->slug) }}">{{ __('site.directory.view') }}</a>
                </div>
            </article>
        @endforeach
    </div>
</section>
@endsection
