<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Support\Locales;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Public directory of listed businesses: /prevadzky/{category}/{city}.
 * Category slugs are localized (lang/{locale}/tenancy.php, key category_slugs); city
 * slugs are derived from the tenant's city name. Pages exist only when they
 * have at least one listed business — no empty (thin) pages.
 */
class DirectoryController extends Controller
{
    public function index(): View
    {
        $tenants = $this->listed();
        $categories = $tenants->groupBy('category')->map(fn (Collection $group, string $category) => [
            'key' => $category,
            'label' => __("tenancy.categories.{$category}"),
            'slug' => self::categorySlug($category),
            'count' => $group->count(),
            'cities' => $group->pluck('city')->filter()->map(fn ($c) => ['name' => $c, 'slug' => Str::slug($c)])->unique('slug')->sortBy('name')->values()->all(),
        ])->sortBy('label')->values();

        return view('site.directory-index', ['categories' => $categories, 'total' => $tenants->count()]);
    }

    public function category(string $category): View
    {
        $key = self::categoryKey($category) ?? abort(404);
        $tenants = $this->listed()->where('category', $key)->sortBy('name')->values();
        abort_if($tenants->isEmpty(), 404);

        return view('site.directory-list', [
            'categoryKey' => $key,
            'categoryLabel' => __("tenancy.categories.{$key}"),
            'city' => null,
            'tenants' => $tenants,
            'cities' => $tenants->pluck('city')->filter()->map(fn ($c) => ['name' => $c, 'slug' => Str::slug($c)])->unique('slug')->sortBy('name')->values(),
        ]);
    }

    public function city(string $category, string $city): View
    {
        $key = self::categoryKey($category) ?? abort(404);
        $tenants = $this->listed()->where('category', $key)->filter(fn (Tenant $t) => Str::slug((string) $t->city) === $city)->sortBy('name')->values();
        abort_if($tenants->isEmpty(), 404);

        return view('site.directory-list', [
            'categoryKey' => $key,
            'categoryLabel' => __("tenancy.categories.{$key}"),
            'city' => $tenants->first()->city,
            'tenants' => $tenants,
            'cities' => collect(),
        ]);
    }

    /** @return Collection<int, Tenant> */
    private function listed(): Collection
    {
        return Cache::remember('directory:listed', 300, fn () => Tenant::query()->listed()->orderBy('name')->get(['id', 'slug', 'name', 'category', 'country', 'city', 'address', 'description']));
    }

    public static function categorySlug(string $key, ?string $locale = null): string
    {
        return __("tenancy.category_slugs.{$key}", [], $locale) ?: $key;
    }

    public static function categoryKey(string $slug): ?string
    {
        foreach (array_keys(config('tenancy.categories')) as $key) {
            if (self::categorySlug($key) === $slug || $key === $slug) {
                return $key;
            }
        }

        return null;
    }

    public static function categoryUrl(string $key, ?string $locale = null): string
    {
        return Locales::route('directory.category', ['category' => self::categorySlug($key, $locale)], $locale);
    }

    public static function cityUrl(string $key, string $city, ?string $locale = null): string
    {
        return Locales::route('directory.city', ['category' => self::categorySlug($key, $locale), 'city' => Str::slug($city)], $locale);
    }
}
