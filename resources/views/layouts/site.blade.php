@php
    use App\Support\Locales;
    $locale = app()->getLocale();
    $alternates = $alternates ?? Locales::alternates();
    $title = trim($__env->yieldContent('title', __('site.meta.title')));
    $description = trim($__env->yieldContent('description', __('site.meta.description')));
    $canonical = trim($__env->yieldContent('canonical', url()->current()));
    $robots = trim($__env->yieldContent('robots', 'index, follow'));
    $ogImage = trim($__env->yieldContent('og_image', asset('images/og-'.$locale.'.png')));
@endphp
<!DOCTYPE html>
<html lang="{{ Locales::TAGS[$locale] ?? $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="robots" content="{{ $robots }}">
    <link rel="canonical" href="{{ $canonical }}">
    @foreach ($alternates as $altLocale => $url)
        <link rel="alternate" hreflang="{{ Locales::TAGS[$altLocale] }}" href="{{ $url }}">
    @endforeach
    @if (isset($alternates[Locales::DEFAULT]))
        <link rel="alternate" hreflang="x-default" href="{{ $alternates[Locales::DEFAULT] }}">
    @endif
    <meta property="og:type" content="{{ trim($__env->yieldContent('og_type', 'website')) }}">
    <meta property="og:site_name" content="{{ __('site.meta.site_name') }}">
    <meta property="og:locale" content="{{ str_replace('-', '_', Locales::TAGS[$locale] === 'en' ? 'en_GB' : Locales::TAGS[$locale]) }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="theme-color" content="#1E6B5E">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='8' fill='%231E6B5E'/%3E%3Crect x='6' y='6' width='20' height='3.1' rx='1.55' fill='%23fff' opacity='.55'/%3E%3Crect x='6' y='12.7' width='8.6' height='4.2' rx='2.1' fill='%23fff' opacity='.38'/%3E%3Crect x='17.4' y='12.7' width='8.6' height='4.2' rx='2.1' fill='%23E4B457'/%3E%3Crect x='6' y='19.4' width='8.6' height='4.2' rx='2.1' fill='%23fff' opacity='.38'/%3E%3Crect x='17.4' y='19.4' width='8.6' height='4.2' rx='2.1' fill='%23fff' opacity='.38'/%3E%3C/svg%3E">
    <link rel="apple-touch-icon" href="{{ asset('logo.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    @stack('head')
    <style>
        :root{--paper:#F6F5F1;--ink:#14231E;--soft:#4A5A54;--mute:#7C8A85;--sea:#1E6B5E;--sea-deep:#154D44;--brass:#B9893B;--mist:#E4EEEA;--line:#D9DDD8;--white:#fff;--max:72rem}
        *{box-sizing:border-box}html{scroll-behavior:smooth;-webkit-text-size-adjust:100%}
        body{margin:0;background:var(--paper);color:var(--ink);font:400 17px/1.55 Manrope,system-ui,sans-serif;-webkit-font-smoothing:antialiased}
        a{color:inherit}img{max-width:100%;display:block}
        ::selection{background:var(--brass);color:#fff}
        :focus-visible{outline:3px solid var(--brass);outline-offset:3px;border-radius:2px}
        .wrap{width:100%;max-width:var(--max);margin-inline:auto;padding-inline:clamp(1rem,4vw,2.5rem)}
        .display,.h2,.h3{font-family:"Bricolage Grotesque",Manrope,sans-serif;font-weight:700;letter-spacing:-.02em;text-wrap:balance;margin:0}
        .display{font-size:clamp(2.4rem,6vw,4.5rem);line-height:1.02}
        .h2{font-size:clamp(1.8rem,3.6vw,2.6rem);line-height:1.1}
        .h3{font-size:1.2rem;line-height:1.3}
        .soft{color:var(--soft)}.mute{color:var(--mute)}.small{font-size:.92rem}
        .lead{font-size:1.15rem;max-width:36rem}
        .btn{display:inline-flex;align-items:center;gap:.5rem;border:2px solid var(--sea);background:var(--sea);color:#fff;border-radius:999px;padding:.85rem 1.4rem;font:600 1rem Manrope,sans-serif;text-decoration:none;cursor:pointer;transition:transform .12s,background .12s}
        .btn:hover{background:var(--sea-deep);border-color:var(--sea-deep);transform:translateY(-1px)}
        .btn.ghost{background:transparent;color:var(--sea)}.btn.ghost:hover{background:var(--mist)}
        .btn.brass{background:var(--brass);border-color:var(--brass)}.btn.brass:hover{filter:brightness(.95)}
        .band{padding-block:clamp(3.5rem,8vw,6.5rem)}.band.mist{background:var(--mist)}.band.ink{background:var(--ink);color:var(--paper)}
        .band.ink .soft{color:#B9C6C1}.band.ink .mute{color:#8FA09A}
        header.top{position:sticky;top:0;z-index:20;background:rgba(246,245,241,.9);backdrop-filter:saturate(1.4) blur(10px);border-bottom:1px solid var(--line)}
        header.top .wrap{display:flex;align-items:center;justify-content:space-between;gap:1rem;min-height:4.25rem}
        .brand{display:inline-flex;align-items:center;gap:.6rem;text-decoration:none;font-family:"Bricolage Grotesque",sans-serif;font-weight:700;font-size:1.15rem}
        .brand i{width:1.9rem;height:1.9rem;border-radius:.55rem;background:var(--sea);display:grid;place-items:center}
        nav.main{display:none;gap:1.6rem;font-weight:500;font-size:.96rem}nav.main a{text-decoration:none}nav.main a:hover{text-decoration:underline;text-underline-offset:4px}
        @media(min-width:64rem){nav.main{display:flex}}
        .lang{display:flex;gap:.15rem}.lang a{text-decoration:none;font-size:.78rem;font-weight:700;letter-spacing:.04em;text-transform:uppercase;padding:.3rem .5rem;border-radius:.4rem}
        .lang a[aria-current]{background:var(--ink);color:var(--paper)}.lang a:not([aria-current]):hover{background:var(--mist)}
        .hero{display:grid;gap:3rem;align-items:center}@media(min-width:64rem){.hero{grid-template-columns:7fr 5fr}}
        .trust{font-size:.92rem;color:var(--soft)}
        /* device mock — the memorable element */
        .device{position:relative;width:min(100%,24rem);margin-inline:auto;background:var(--white);border:1px solid var(--line);border-radius:1.6rem;box-shadow:0 40px 80px -48px rgba(20,35,30,.55),0 0 0 8px var(--mist);overflow:hidden}
        .device .bar{display:flex;align-items:center;justify-content:space-between;padding:.9rem 1.1rem;border-bottom:1px solid var(--line);font-weight:700;font-size:.95rem}
        .device .bar span:last-child{font-size:.75rem;color:var(--mute);font-weight:600}
        .device .sec{padding:.85rem 1.1rem .4rem;font-size:.8rem;color:var(--mute);font-weight:600;letter-spacing:.02em}
        .svc{display:flex;justify-content:space-between;gap:.75rem;padding:.7rem 1.1rem;border-top:1px solid var(--line)}
        .svc b{font-weight:600;font-size:.95rem}.svc small{color:var(--mute);display:block;font-size:.8rem}.svc .p{font-weight:700;white-space:nowrap}
        .svc.on{background:var(--mist)}
        .slots{display:grid;grid-template-columns:repeat(4,1fr);gap:.45rem;padding:.6rem 1.1rem 1rem}
        .slots span{border:1px solid var(--line);border-radius:.55rem;text-align:center;padding:.45rem 0;font-size:.85rem;font-weight:600}
        .slots span.on{background:var(--sea);color:#fff;border-color:var(--sea)}.slots span.off{color:var(--line);text-decoration:line-through}
        .device .cta{margin:.2rem 1.1rem 1.1rem;background:var(--sea);color:#fff;text-align:center;border-radius:.8rem;padding:.8rem;font-weight:700}
        .stamp{position:absolute;right:-.6rem;top:1.1rem;transform:rotate(6deg);background:var(--brass);color:#fff;font-family:"Bricolage Grotesque",sans-serif;font-weight:700;font-size:1.05rem;padding:.45rem .9rem;border-radius:.6rem;box-shadow:0 8px 20px -10px rgba(0,0,0,.4)}
        .steps{counter-reset:s;display:grid;gap:2rem}@media(min-width:48rem){.steps{grid-template-columns:repeat(3,1fr)}}
        .step{counter-increment:s;padding-top:.5rem;border-top:3px solid var(--sea)}
        .step::before{content:counter(s);display:block;font-family:"Bricolage Grotesque",sans-serif;font-weight:700;font-size:2.4rem;line-height:1;color:var(--sea);margin:.6rem 0 .4rem}
        .feat{display:grid;gap:1.4rem 3rem}@media(min-width:48rem){.feat{grid-template-columns:1fr 1fr}}
        .feat div{padding-left:1.1rem;border-left:2px solid var(--brass)}.feat b{display:block;font-weight:700;margin-bottom:.15rem}
        table.plans{width:100%;border-collapse:collapse;font-size:.98rem;background:var(--white);border:1px solid var(--line);border-radius:1rem;overflow:hidden}
        table.plans th,table.plans td{padding:.85rem 1rem;text-align:left;border-bottom:1px solid var(--line)}
        table.plans th{vertical-align:top;background:var(--mist)}table.plans td:nth-child(2),table.plans td:nth-child(3),table.plans th:nth-child(2),table.plans th:nth-child(3){text-align:center}
        table.plans tr:last-child td{border-bottom:0}table.plans th .price{font-family:"Bricolage Grotesque",sans-serif;font-size:2.2rem;line-height:1;display:block;margin:.2rem 0}
        .tblwrap{overflow-x:auto}
        .soon-badge{display:inline-block;vertical-align:middle;margin-left:.5rem;background:var(--brass);color:#fff;font-family:Manrope,sans-serif;font-weight:700;font-size:.62rem;letter-spacing:.06em;text-transform:uppercase;padding:.2rem .45rem;border-radius:999px}
        .chips{display:flex;flex-wrap:wrap;gap:.6rem}.chips a{text-decoration:none;border:1.5px solid var(--line);background:var(--white);border-radius:999px;padding:.5rem .95rem;font-weight:600;font-size:.93rem}.chips a:hover{border-color:var(--sea);color:var(--sea)}
        .faq details{border-top:1px solid var(--line);padding:1rem 0}.faq details:last-child{border-bottom:1px solid var(--line)}
        .faq summary{cursor:pointer;font-weight:700;list-style:none;display:flex;justify-content:space-between;gap:1rem}.faq summary::-webkit-details-marker{display:none}
        .faq summary::after{content:'+';color:var(--mute);font-weight:400;font-size:1.4rem;line-height:1}.faq details[open] summary::after{content:'–'}
        .faq p{margin:.6rem 0 0;color:var(--soft);max-width:62ch}
        footer.site{border-top:1px solid var(--line);padding:3rem 0 2rem;font-size:.93rem}
        footer.site .cols{display:grid;gap:2rem}@media(min-width:48rem){footer.site .cols{grid-template-columns:1.4fr 1fr 1fr}}
        footer.site ul{list-style:none;margin:.5rem 0 0;padding:0;display:grid;gap:.4rem}footer.site a{text-decoration:none}footer.site a:hover{text-decoration:underline}
        /* legal + directory + profile */
        .doc{max-width:46rem}.doc h1{font-family:"Bricolage Grotesque",sans-serif;font-size:clamp(1.9rem,4vw,2.6rem);letter-spacing:-.02em;margin:0 0 .5rem}
        .doc h2{font-family:"Bricolage Grotesque",sans-serif;font-size:1.35rem;margin:2rem 0 .6rem}.doc h3{font-size:1.05rem;margin:1.4rem 0 .4rem}
        .doc p,.doc li{color:#2F3D38;line-height:1.65}.doc table{width:100%;border-collapse:collapse;font-size:.92rem;margin:1rem 0}.doc th,.doc td{border:1px solid var(--line);padding:.55rem .7rem;text-align:left;vertical-align:top}
        .doc .legal-meta{color:var(--mute);font-size:.9rem}
        .toc{position:sticky;top:5.5rem;align-self:start;display:grid;gap:.35rem;font-size:.92rem}.toc a{text-decoration:none;padding:.35rem .6rem;border-radius:.45rem}.toc a[aria-current]{background:var(--mist);font-weight:700}
        .split{display:grid;gap:2.5rem}@media(min-width:64rem){.split{grid-template-columns:16rem 1fr}}
        .list{display:grid;gap:1rem}@media(min-width:48rem){.list{grid-template-columns:1fr 1fr}}
        .biz{background:var(--white);border:1px solid var(--line);border-radius:1rem;padding:1.2rem;display:grid;gap:.4rem}
        .biz .h3 a{text-decoration:none}.biz .meta{font-size:.9rem;color:var(--mute)}
        .profile-head{display:grid;gap:1.5rem;align-items:start}@media(min-width:48rem){.profile-head{grid-template-columns:1fr auto}}
        .price-table{width:100%;border-collapse:collapse}.price-table td{padding:.7rem 0;border-bottom:1px solid var(--line);vertical-align:top}.price-table td:last-child{text-align:right;white-space:nowrap;font-weight:700}
        .bk-page{padding:3rem 1rem}.bk-status{max-width:34rem;margin:0 auto;text-align:center}.bk-status h1{font-family:"Bricolage Grotesque",sans-serif;font-size:2rem;margin:.6rem 0}.bk-status-icon{width:3rem;height:3rem;margin:0 auto;color:var(--sea)}.bk-btn-row{display:flex;gap:.6rem;justify-content:center;flex-wrap:wrap;margin-top:1.2rem}.bk-btn{display:inline-flex;align-items:center;border:2px solid var(--sea);border-radius:999px;padding:.7rem 1.2rem;font-weight:600;text-decoration:none;background:#fff;color:var(--sea);cursor:pointer;font:inherit}.bk-btn.is-primary{background:var(--sea);color:#fff}
        .skip{position:absolute;left:-999px;top:0}.skip:focus{left:1rem;top:1rem;background:var(--ink);color:#fff;padding:.5rem .8rem;border-radius:.4rem;z-index:99}
        @media(prefers-reduced-motion:reduce){*{transition:none!important;animation:none!important;scroll-behavior:auto!important}}
    </style>
    @stack('styles')
</head>
<body>
    <a class="skip" href="#main">{{ __('common.skip_to_content', [], null) === 'common.skip_to_content' ? 'Skip to content' : __('common.skip_to_content') }}</a>
    <header class="top">
        <div class="wrap">
            <a class="brand" href="{{ Locales::route('home') }}" aria-label="rezervuj-ma.online"><i aria-hidden="true">@include('partials.brand-mark')</i>rezervuj-ma</a>
            <nav class="main" aria-label="Main">
                <a href="{{ Locales::route('home') }}#ako">{{ __('site.nav.how') }}</a>
                <a href="{{ Locales::route('home') }}#funkcie">{{ __('site.nav.features') }}</a>
                <a href="{{ Locales::route('home') }}#cennik">{{ __('site.nav.pricing') }}</a>
                <a href="{{ Locales::route('directory.index') }}">{{ __('site.nav.directory') }}</a>
                <a href="{{ Locales::route('home') }}#faq">{{ __('site.nav.faq') }}</a>
            </nav>
            <div style="display:flex;align-items:center;gap:.75rem">
                <div class="lang" aria-label="{{ __('site.nav.language') }}">
                    @foreach (Locales::SUPPORTED as $l)
                        <a href="{{ $alternates[$l] ?? Locales::route('home', [], $l) }}" @if($l === $locale) aria-current="page" @endif hreflang="{{ $l }}">{{ $l }}</a>
                    @endforeach
                </div>
                <a class="btn" style="padding:.6rem 1rem;font-size:.92rem" href="{{ Locales::route('register') }}">{{ __('site.nav.signup') }}</a>
            </div>
        </div>
    </header>

    <main id="main">
        @yield('content')
    </main>

    <footer class="site">
        <div class="wrap cols">
            <div>
                <a class="brand" href="{{ Locales::route('home') }}"><i aria-hidden="true">@include('partials.brand-mark')</i>rezervuj-ma.online</a>
                <p class="soft" style="max-width:26rem">{{ __('site.footer.tagline') }}</p>
                <p class="mute small">{{ __('site.footer.not_affiliated') }}</p>
            </div>
            <div>
                <b>{{ __('site.footer.product') }}</b>
                <ul>
                    <li><a href="{{ Locales::route('home') }}#cennik">{{ __('site.nav.pricing') }}</a></li>
                    <li><a href="{{ Locales::route('directory.index') }}">{{ __('site.nav.directory') }}</a></li>
                    <li><a href="{{ Locales::route('register') }}">{{ __('site.nav.signup') }}</a></li>
                    <li><a href="{{ Locales::route('home') }}#faq">{{ __('site.nav.faq') }}</a></li>
                </ul>
            </div>
            <div>
                <b>{{ __('site.footer.legal') }}</b>
                <ul>
                    @foreach (\App\Http\Controllers\Platform\SiteController::PUBLIC_LEGAL_DOCS as $doc)
                        <li><a href="{{ Locales::route('legal.show', ['doc' => $doc]) }}">{{ __("site.legal.docs.{$doc}") }}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="wrap mute small" style="margin-top:2rem">© {{ date('Y') }} rezervuj-ma.online</div>
    </footer>
    @stack('scripts')
</body>
</html>
