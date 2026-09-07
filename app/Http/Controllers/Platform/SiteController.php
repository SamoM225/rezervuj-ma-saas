<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use App\Models\Tenant;
use App\Support\Locales;
use App\Support\Tenancy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Marketing site + legal hub (per locale).
 */
class SiteController extends Controller
{
    /** Every renderable document (used for routing and internal cross-links). */
    public const LEGAL_DOCS = ['terms', 'dpa', 'privacy', 'cookies', 'aup', 'refunds', 'imprint', 'subprocessors'];

    /**
     * Documents listed publicly (footer, sitemap, /legal index). While the
     * service is free and pre-launch, the imprint (operator identity) and the
     * refund policy are not surfaced; the pages remain reachable by URL so
     * internal cross-links do not break.
     */
    public const PUBLIC_LEGAL_DOCS = ['terms', 'privacy', 'cookies', 'dpa', 'aup', 'subprocessors'];

    public function home(): View
    {
        return view('site.landing', [
            'preview' => $this->preview(),
            'stats' => Cache::remember('site:stats', 600, fn () => [
                'tenants' => Tenant::query()->listed()->count(),
            ]),
        ]);
    }

    public function pricing(): RedirectResponse
    {
        return redirect(Locales::route('home').'#cennik');
    }

    public function legalIndex(): View
    {
        return view('site.legal-index', ['docs' => self::PUBLIC_LEGAL_DOCS]);
    }

    public function legalShow(string $doc): View
    {
        abort_unless(in_array($doc, self::LEGAL_DOCS, true), 404);
        $locale = app()->getLocale();
        $view = "legal.{$doc}.{$locale}";
        if (! view()->exists($view)) {
            $view = "legal.{$doc}.sk";
        }
        abort_unless(view()->exists($view), 404);

        $links = [];
        foreach (self::LEGAL_DOCS as $key) {
            $links[$key] = Locales::route('legal.show', ['doc' => $key]);
        }

        return view('site.legal-show', [
            'doc' => $doc,
            'docs' => self::PUBLIC_LEGAL_DOCS,
            'body' => $view,
            'version' => (string) config("legal.versions.{$doc}", config('legal.versions.terms')),
            'effective' => '1. 10. 2026',
            'operator' => config('legal.operator'),
            'siteUrl' => rtrim(config('app.url'), '/'),
            'emailProvider' => config('mail.mailers.smtp.host') ?: 'e-mail provider',
            'links' => $links,
        ]);
    }

    /**
     * Real data for the hero mock-up: the demo tenant's services (or a static
     * fallback so the page never breaks on an empty database).
     *
     * @return array{name: string, slug: ?string, groups: array<int, array{name: string, services: array<int, array{name: string, duration: int, price: string}>}>}
     */
    private function preview(): array
    {
        return Cache::remember('site:preview:'.app()->getLocale(), 600, function () {
            $demo = Tenant::query()->where('slug', 'demo')->first();
            if (! $demo) {
                return [
                    'name' => 'Demo Beauty Studio', 'slug' => null,
                    'groups' => [
                        ['name' => 'Kozmetika', 'services' => [['name' => 'Čistenie pleti', 'duration' => 60, 'price' => '45 €'], ['name' => 'Hydratačné ošetrenie', 'duration' => 45, 'price' => '39 €']]],
                        ['name' => 'Nechty', 'services' => [['name' => 'Gélové nechty', 'duration' => 90, 'price' => '35 €'], ['name' => 'Manikúra', 'duration' => 45, 'price' => '20 €']]],
                    ],
                ];
            }

            return Tenancy::runAs($demo, function () use ($demo) {
                $groups = Category::query()->with(['services' => fn ($q) => $q->orderBy('name')->limit(3)])->orderBy('name')->limit(3)->get()
                    ->map(fn (Category $category) => [
                        'name' => $category->getTranslation('name'),
                        'services' => $category->services->map(fn (Service $service) => [
                            'name' => $service->getTranslation('name'),
                            'duration' => (int) $service->duration,
                            'price' => is_numeric($service->price) ? number_format((float) $service->price, 0, ',', ' ').' €' : (string) $service->price,
                        ])->all(),
                    ])->all();

                return ['name' => $demo->name, 'slug' => $demo->slug, 'groups' => $groups];
            });
        });
    }
}
