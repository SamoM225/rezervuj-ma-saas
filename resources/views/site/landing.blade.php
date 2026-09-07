@extends('layouts.site')

@php
    use App\Http\Controllers\Platform\DirectoryController;
    use App\Support\Locales;
    $faq = __('site.faq.items');
    $rows = __('site.pricing.rows');
    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            ['@type' => 'Organization', 'name' => 'rezervuj-ma.online', 'url' => url('/'), 'logo' => asset('images/og-sk.png')],
            ['@type' => 'SoftwareApplication', 'name' => 'rezervuj-ma.online', 'applicationCategory' => 'BusinessApplication', 'operatingSystem' => 'Web', 'url' => url('/'),
                'offers' => [
                    ['@type' => 'Offer', 'name' => 'Free', 'price' => '0', 'priceCurrency' => 'EUR'],
                    ['@type' => 'Offer', 'name' => 'Pro', 'price' => '5', 'priceCurrency' => 'EUR', 'billingIncrement' => 'P1M'],
                ]],
            ['@type' => 'FAQPage', 'mainEntity' => array_map(fn ($i) => ['@type' => 'Question', 'name' => $i['q'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $i['a']]], $faq)],
        ],
    ];
    $demoUrl = $preview['slug'] ? url('/'.$preview['slug'].'/booking') : Locales::route('register');
@endphp

@section('canonical', Locales::route('home'))

@push('head')
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<section class="wrap band hero" style="padding-top:clamp(2.5rem,6vw,5rem)">
    <div>
        <h1 class="display">{{ __('site.hero.title') }}<br><span style="color:var(--sea)">{{ __('site.hero.title_2') }}</span></h1>
        <p class="lead soft" style="margin-top:1.4rem">{{ __('site.hero.lead') }}</p>
        <div style="display:flex;flex-wrap:wrap;gap:.75rem;margin-top:1.8rem">
            <a class="btn" href="{{ Locales::route('register') }}">{{ __('site.hero.cta') }}</a>
            <a class="btn ghost" href="{{ $demoUrl }}">{{ __('site.hero.demo') }}</a>
        </div>
        <p class="trust" style="margin-top:1.1rem">{{ __('site.hero.trust') }}</p>
    </div>

    <div class="device" aria-label="{{ __('site.hero.preview_label') }}">
        <span class="stamp" aria-hidden="true">5 € · ∞</span>
        <div class="bar"><span>{{ $preview['name'] }}</span><span>{{ __('site.hero.preview_label') }}</span></div>
        <div class="sec">{{ __('site.hero.preview_step') }}</div>
        @foreach (array_slice($preview['groups'], 0, 2) as $gi => $group)
            @foreach (array_slice($group['services'], 0, $gi === 0 ? 2 : 1) as $si => $service)
                <div class="svc {{ $gi === 0 && $si === 0 ? 'on' : '' }}">
                    <div><b>{{ $service['name'] }}</b><small>{{ $group['name'] }} · {{ __('site.hero.preview_min', ['n' => $service['duration']]) }}</small></div>
                    <div class="p">{{ $service['price'] }}</div>
                </div>
            @endforeach
        @endforeach
        <div class="sec">{{ __('site.hero.preview_time') }}</div>
        <div class="slots" aria-hidden="true">
            <span class="off">9:00</span><span>9:45</span><span class="on">10:30</span><span>11:15</span>
            <span>13:00</span><span class="off">13:45</span><span>14:30</span><span>16:00</span>
        </div>
        <div class="cta" aria-hidden="true">{{ __('site.hero.preview_cta') }}</div>
    </div>
</section>

<section id="ako" class="band ink">
    <div class="wrap">
        <h2 class="h2" style="margin-bottom:2.5rem">{{ __('site.how.title') }}</h2>
        <ol class="steps" style="list-style:none;margin:0;padding:0">
            @foreach (__('site.how.steps') as $step)
                <li class="step"><h3 class="h3">{{ $step['t'] }}</h3><p class="soft" style="margin:.5rem 0 0">{{ $step['d'] }}</p></li>
            @endforeach
        </ol>
    </div>
</section>

<section id="funkcie" class="band">
    <div class="wrap">
        <h2 class="h2">{{ __('site.features.title') }}</h2>
        <p class="lead soft" style="margin:.8rem 0 2.5rem">{{ __('site.features.lead') }}</p>
        <div class="feat">
            @foreach (__('site.features.items') as $item)
                <div><b>{{ $item['t'] }}</b><span class="soft">{{ $item['d'] }}</span></div>
            @endforeach
        </div>
    </div>
</section>

<section id="cennik" class="band mist">
    <div class="wrap">
        <h2 class="h2">{{ __('site.pricing.title') }}</h2>
        <p class="lead soft" style="margin:.8rem 0 2rem">{{ __('site.pricing.lead') }}</p>
        <div class="tblwrap">
        <table class="plans">
            <thead>
                <tr>
                    <th style="width:44%"></th>
                    <th>{{ __('site.pricing.free') }}<span class="price">{{ __('site.pricing.free_price') }}</span><span class="mute small">{{ __('site.pricing.free_note') }}</span></th>
                    <th>{{ __('site.pricing.pro') }}@unless(config('billing.enabled')) <span class="soon-badge">{{ __('site.pricing.pro_soon') }}</span>@endunless<span class="price">{{ __('site.pricing.pro_price') }}</span><span class="mute small">{{ __('site.pricing.per_month') }} · {{ __('site.pricing.pro_yearly') }}</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr><td>{{ $row['k'] }}</td><td>{{ $row['f'] }}</td><td>{{ $row['p'] }}</td></tr>
                @endforeach
                <tr>
                    <td></td>
                    <td><a class="btn ghost" style="padding:.6rem 1rem;font-size:.92rem" href="{{ Locales::route('register') }}">{{ __('site.pricing.cta_free') }}</a></td>
                    @if(config('billing.enabled'))
                        <td><a class="btn brass" style="padding:.6rem 1rem;font-size:.92rem" href="{{ Locales::route('register') }}">{{ __('site.pricing.cta_pro') }}</a></td>
                    @else
                        <td><span class="btn brass" style="padding:.6rem 1rem;font-size:.92rem;opacity:.55;cursor:default;pointer-events:none" aria-disabled="true">{{ __('site.pricing.cta_pro_soon') }}</span></td>
                    @endif
                </tr>
            </tbody>
        </table>
        </div>
        <p class="soft small" style="margin-top:1.2rem;max-width:44rem">{{ config('billing.enabled') ? __('site.pricing.payment') : __('site.pricing.payment_soon') }}</p>
        <p class="mute small" style="max-width:44rem">{{ __('site.pricing.compare') }}</p>
    </div>
</section>

<section class="band">
    <div class="wrap">
        <h2 class="h2">{{ __('site.categories.title') }}</h2>
        <p class="lead soft" style="margin:.8rem 0 1.8rem">{{ __('site.categories.lead') }}</p>
        <div class="chips">
            @foreach (array_keys(config('tenancy.categories')) as $key)
                <a href="{{ Locales::route('directory.index') }}#{{ $key }}">{{ __("tenancy.categories.{$key}") }}</a>
            @endforeach
        </div>
        <p style="margin-top:1.5rem"><a class="btn ghost" href="{{ Locales::route('directory.index') }}">{{ __('site.categories.browse') }}</a></p>
    </div>
</section>

<section id="faq" class="band mist">
    <div class="wrap" style="max-width:52rem">
        <h2 class="h2" style="margin-bottom:1.8rem">{{ __('site.faq.title') }}</h2>
        <div class="faq">
            @foreach ($faq as $item)
                <details><summary>{{ $item['q'] }}</summary><p>{{ $item['a'] }}</p></details>
            @endforeach
        </div>
        <p style="margin-top:2.2rem"><a class="btn" href="{{ Locales::route('register') }}">{{ __('site.hero.cta') }}</a></p>
    </div>
</section>
@endsection
