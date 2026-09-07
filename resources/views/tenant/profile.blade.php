@extends('layouts.site')
@php
    use App\Http\Controllers\Platform\DirectoryController;
    use App\Support\Locales;
    $categoryLabel = __("tenancy.categories.{$tenant->category}");
    $title = $tenant->name.' — '.$categoryLabel.($tenant->city ? ', '.$tenant->city : '');
    $description = $tenant->description ?: __('site.profile.meta_desc', ['name' => $tenant->name, 'category' => mb_strtolower($categoryLabel), 'city' => $tenant->city ?: $tenant->country]);
    $self = url('/'.$tenant->slug);
    $bookingUrl = url('/'.$tenant->slug.'/booking');
    $alternates = [];
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => $schemaType,
        '@id' => $self.'#business',
        'name' => $tenant->name,
        'url' => $self,
        'description' => $description,
        'image' => $logo ? \Illuminate\Support\Facades\Storage::url($logo) : null,
        'telephone' => $phone ?: null,
        'email' => $email ?: null,
        'address' => $address || $tenant->city ? ['@type' => 'PostalAddress', 'streetAddress' => $address ?: null, 'addressLocality' => $tenant->city ?: null, 'addressCountry' => $tenant->country !== 'XX' ? $tenant->country : null] : null,
        'priceRange' => $categories->flatMap->services->filter(fn ($s) => is_numeric($s->price))->pluck('price')->map(fn ($p) => (float) $p)->whenNotEmpty(fn ($c) => collect([number_format($c->min(), 0).'–'.number_format($c->max(), 0).' '.$currency]))->first(),
        'potentialAction' => ['@type' => 'ReserveAction', 'target' => ['@type' => 'EntryPoint', 'urlTemplate' => $bookingUrl, 'actionPlatform' => ['http://schema.org/DesktopWebPlatform', 'http://schema.org/MobileWebPlatform']], 'result' => ['@type' => 'Reservation', 'name' => __('site.profile.book')]],
        'makesOffer' => $categories->flatMap->services->take(60)->map(fn ($s) => array_filter([
            '@type' => 'Offer',
            'itemOffered' => ['@type' => 'Service', 'name' => $s->getTranslation('name'), 'description' => $s->getTranslation('description') ?: null],
            'price' => is_numeric($s->price) ? number_format((float) $s->price, 2, '.', '') : null,
            'priceCurrency' => is_numeric($s->price) ? $currency : null,
        ]))->values()->all(),
    ];
    $schema = array_filter($schema, fn ($v) => $v !== null && $v !== []);
@endphp
@section('title', $title)
@section('description', \Illuminate\Support\Str::limit($description, 155))
@section('canonical', $self)
@section('robots', $indexable ? 'index, follow' : 'noindex, nofollow')
@section('og_type', 'business.business')
@if ($logo)@section('og_image', \Illuminate\Support\Facades\Storage::url($logo))@endif
@push('head')<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>@endpush

@section('content')
<section class="wrap band" style="padding-top:clamp(2rem,5vw,4rem)">
    <div class="profile-head">
        <div>
            <p class="mute small"><a href="{{ DirectoryController::categoryUrl($tenant->category) }}" style="text-decoration:none">{{ $categoryLabel }}</a>@if($tenant->city) · <a href="{{ DirectoryController::cityUrl($tenant->category, $tenant->city) }}" style="text-decoration:none">{{ $tenant->city }}</a>@endif</p>
            <div style="display:flex;gap:1rem;align-items:center;margin-top:.4rem">
                @if ($logo)<img src="{{ \Illuminate\Support\Facades\Storage::url($logo) }}" alt="" width="64" height="64" style="width:4rem;height:4rem;object-fit:contain;border-radius:.9rem;background:#fff;border:1px solid var(--line);padding:.3rem">@endif
                <h1 class="display" style="font-size:clamp(2rem,5vw,3.2rem)">{{ $tenant->name }}</h1>
            </div>
            @if ($tenant->description)<p class="lead soft" style="margin-top:1rem">{{ $tenant->description }}</p>@endif
        </div>
        <a class="btn brass" style="font-size:1.05rem;padding:.95rem 1.5rem" href="{{ $bookingUrl }}">{{ __('site.profile.book') }}</a>
    </div>
</section>

<section class="wrap" style="display:grid;gap:3rem;padding-bottom:4rem;grid-template-columns:1fr">
    <div style="display:grid;gap:3rem;@media(min-width:48rem){grid-template-columns:1fr}">
        @if ($categories->isNotEmpty())
        <div>
            <h2 class="h2" style="font-size:1.6rem;margin-bottom:1rem">{{ __('site.profile.services') }}</h2>
            @foreach ($categories as $category)
                <h3 class="h3" style="margin:1.4rem 0 .4rem;color:var(--sea)">{{ $category->getTranslation('name') }}</h3>
                <table class="price-table">
                    @foreach ($category->services as $service)
                        <tr>
                            <td><b style="font-weight:600">{{ $service->getTranslation('name') }}</b><span class="mute small" style="display:block">{{ __('site.profile.minutes', ['n' => (int) $service->duration]) }}@if($service->getTranslation('description')) · {{ \Illuminate\Support\Str::limit($service->getTranslation('description'), 120) }}@endif</span></td>
                            <td>{{ $price($service) }}</td>
                        </tr>
                    @endforeach
                </table>
            @endforeach
        </div>
        @endif

        <div style="display:grid;gap:2rem;grid-template-columns:repeat(auto-fit,minmax(14rem,1fr))">
            @if ($workers->isNotEmpty())
            <div>
                <h2 class="h3" style="margin-bottom:.6rem">{{ __('site.profile.team') }}</h2>
                <ul style="list-style:none;margin:0;padding:0;display:grid;gap:.5rem">
                    @foreach ($workers as $worker)
                        <li style="display:flex;align-items:center;gap:.6rem">
                            @if ($worker->avatar_path)<img src="{{ \Illuminate\Support\Facades\Storage::url($worker->avatar_path) }}" alt="" width="36" height="36" style="width:2.25rem;height:2.25rem;border-radius:50%;object-fit:cover">@else<span style="width:2.25rem;height:2.25rem;border-radius:50%;background:var(--mist);display:grid;place-items:center;font-weight:700">{{ mb_strtoupper(mb_substr($worker->name, 0, 1)) }}</span>@endif
                            <span>{{ $worker->name }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div>
                <h2 class="h3" style="margin-bottom:.6rem">{{ __('site.profile.contact') }}</h2>
                <dl style="margin:0;display:grid;gap:.4rem">
                    @if ($address)<div><dt class="mute small">{{ __('site.profile.address') }}</dt><dd style="margin:0">{{ $address }}@if($tenant->city && !str_contains($address, $tenant->city)), {{ $tenant->city }}@endif</dd></div>@elseif($tenant->city)<div><dt class="mute small">{{ __('site.profile.address') }}</dt><dd style="margin:0">{{ $tenant->city }}</dd></div>@endif
                    @if ($phone)<div><dt class="mute small">{{ __('site.profile.phone') }}</dt><dd style="margin:0"><a href="tel:{{ preg_replace('/\s+/', '', $phone) }}">{{ $phone }}</a></dd></div>@endif
                    @if ($email)<div><dt class="mute small">{{ __('site.profile.email') }}</dt><dd style="margin:0"><a href="mailto:{{ $email }}">{{ $email }}</a></dd></div>@endif
                </dl>
            </div>
            @if ($openingHours)
            <div>
                <h2 class="h3" style="margin-bottom:.6rem">{{ __('site.profile.hours') }}</h2>
                <p class="soft" style="margin:0;white-space:pre-line">{{ $openingHours }}</p>
            </div>
            @endif
        </div>

        <p><a class="btn" href="{{ $bookingUrl }}">{{ __('site.profile.book') }}</a> <a class="btn ghost" style="margin-left:.5rem" href="{{ DirectoryController::categoryUrl($tenant->category) }}">{{ __('site.profile.more_in', ['category' => $categoryLabel]) }}</a></p>
    </div>
</section>
@endsection
