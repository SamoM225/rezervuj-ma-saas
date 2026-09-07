<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Support\Locales;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * sitemap.xml (platform pages in every locale, legal hub, directory pages
 * that actually have businesses, public tenant profiles) and robots.txt.
 */
class SitemapController extends Controller
{
    public function index(): Response
    {
        $xml = Cache::remember('sitemap:xml', 1800, fn () => $this->build());

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8', 'Cache-Control' => 'public, max-age=1800']);
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /register',
            'Disallow: /*/register',
            'Disallow: /*/admin/',
            'Disallow: /*/worker/',
            'Disallow: /*/login',
            'Disallow: /*/2fa/',
            'Disallow: /*/account/',
            'Disallow: /*/web/',
            'Disallow: /*/booking',
            'Disallow: /*/booking/',
            '',
            'Sitemap: '.url('/sitemap.xml'),
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }

    private function build(): string
    {
        $urls = [];
        $add = function (string $loc, ?string $lastmod = null, string $changefreq = 'weekly', string $priority = '0.6', array $alternates = []) use (&$urls) {
            $urls[] = compact('loc', 'lastmod', 'changefreq', 'priority', 'alternates');
        };

        foreach (['home', 'legal.index', 'directory.index'] as $name) {
            $alternates = Locales::alternates($name);
            foreach ($alternates as $url) {
                $add($url, null, $name === 'home' ? 'weekly' : 'monthly', $name === 'home' ? '1.0' : '0.5', $alternates);
            }
        }
        foreach (SiteController::PUBLIC_LEGAL_DOCS as $doc) {
            $alternates = Locales::alternates('legal.show', ['doc' => $doc]);
            foreach ($alternates as $url) {
                $add($url, null, 'yearly', '0.3', $alternates);
            }
        }

        $tenants = Tenant::query()->listed()->orderBy('id')->get(['slug', 'category', 'city', 'updated_at']);
        foreach ($tenants as $tenant) {
            $add(url('/'.$tenant->slug), $tenant->updated_at?->toAtomString(), 'weekly', '0.7');
        }

        $seen = [];
        foreach ($tenants as $tenant) {
            $catKey = 'c:'.$tenant->category;
            if (! isset($seen[$catKey])) {
                $seen[$catKey] = true;
                $alternates = [];
                foreach (Locales::SUPPORTED as $locale) {
                    $alternates[$locale] = DirectoryController::categoryUrl($tenant->category, $locale);
                }
                foreach ($alternates as $url) {
                    $add($url, null, 'weekly', '0.5', $alternates);
                }
            }
            if ($tenant->city) {
                $cityKey = $catKey.':'.Str::slug($tenant->city);
                if (! isset($seen[$cityKey])) {
                    $seen[$cityKey] = true;
                    $alternates = [];
                    foreach (Locales::SUPPORTED as $locale) {
                        $alternates[$locale] = DirectoryController::cityUrl($tenant->category, $tenant->city, $locale);
                    }
                    foreach ($alternates as $url) {
                        $add($url, null, 'weekly', '0.5', $alternates);
                    }
                }
            }
        }

        $out = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $out .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n";
        foreach ($urls as $u) {
            $out .= "  <url>\n    <loc>".e($u['loc'])."</loc>\n";
            if ($u['lastmod']) {
                $out .= '    <lastmod>'.e($u['lastmod'])."</lastmod>\n";
            }
            $out .= '    <changefreq>'.$u['changefreq']."</changefreq>\n    <priority>".$u['priority']."</priority>\n";
            foreach ($u['alternates'] as $locale => $href) {
                $out .= '    <xhtml:link rel="alternate" hreflang="'.Locales::TAGS[$locale].'" href="'.e($href).'"/>'."\n";
            }
            if ($u['alternates']) {
                $out .= '    <xhtml:link rel="alternate" hreflang="x-default" href="'.e($u['alternates'][Locales::DEFAULT] ?? $u['loc']).'"/>'."\n";
            }
            $out .= "  </url>\n";
        }

        return $out.'</urlset>';
    }
}
