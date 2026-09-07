<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Service;
use App\Models\Tenant;
use App\Support\Tenancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_exists_in_every_locale_with_hreflang_and_structured_data(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('Online rezervácie pre vašu prevádzku')
            ->assertSee('hreflang="sk-SK" href="http://localhost"', false)
            ->assertSee('hreflang="cs-CZ" href="http://localhost/cs"', false)
            ->assertSee('hreflang="en" href="http://localhost/en"', false)
            ->assertSee('hreflang="x-default"', false)
            ->assertSee('<link rel="canonical" href="http://localhost">', false)
            ->assertSee('"@type":"FAQPage"', false)
            ->assertSee('"@type":"SoftwareApplication"', false)
            ->assertHeaderMissing('X-Robots-Tag');

        $this->get('/cs')->assertOk()->assertSee('Online rezervace pro vaši provozovnu')->assertSee('<html lang="cs-CZ">', false);
        $this->get('/en')->assertOk()->assertSee('Online booking for your business')->assertSee('href="http://localhost/en/register"', false);
        $this->get('/de')->assertNotFound();
    }

    public function test_legal_hub_renders_versioned_documents_in_each_language(): void
    {
        $this->get('/legal')->assertOk()->assertSee('Podmienky používania');
        $this->get('/legal/dpa')->assertOk()->assertSee('Zmluva o spracúvaní osobných údajov')->assertSee('Verzia '.config('legal.versions.dpa'));
        $this->get('/cs/legal/terms')->assertOk()->assertSee('Verze');
        $this->get('/en/legal/privacy')->assertOk()->assertSee('Privacy');
        $this->get('/legal/nonsense')->assertNotFound();
    }

    public function test_directory_shows_only_listed_tenants_and_has_no_empty_pages(): void
    {
        $this->get('/prevadzky/kozmetika')->assertNotFound();

        $listed = Tenant::factory()->create(['slug' => 'salon-a', 'name' => 'Salón Anna', 'category' => 'beauty', 'city' => 'Nitra']);
        Tenant::factory()->create(['slug' => 'hidden-b', 'name' => 'Skrytý B', 'category' => 'beauty', 'city' => 'Nitra', 'is_public' => false]);
        Tenant::factory()->suspended()->create(['slug' => 'off-c', 'name' => 'Vypnutý C', 'category' => 'beauty', 'city' => 'Nitra']);

        $this->get('/prevadzky')->assertOk()->assertSee('Kozmetika / estetika')->assertSee('Nitra');
        $this->get('/prevadzky/kozmetika')->assertOk()->assertSee('Salón Anna')->assertDontSee('Skrytý B')->assertDontSee('Vypnutý C')->assertSee('"@type":"ItemList"', false);
        $this->get('/prevadzky/kozmetika/nitra')->assertOk()->assertSee('Salón Anna')->assertSee('hreflang="en" href="http://localhost/en/directory/beauty-salons/nitra"', false);
        $this->get('/en/directory/beauty-salons/nitra')->assertOk()->assertSee('Salón Anna');
        $this->get('/prevadzky/kadernictva')->assertNotFound();
        $this->get('/prevadzky/kozmetika/bratislava')->assertNotFound();

        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=utf-8')
            ->assertSee('<loc>http://localhost/salon-a</loc>', false)
            ->assertDontSee('hidden-b')
            ->assertSee('http://localhost/prevadzky/kozmetika/nitra', false)
            ->assertSee('http://localhost/cs/legal/dpa', false);
        $this->get('/robots.txt')->assertOk()->assertSee('Allow: /')->assertSee('Sitemap: http://localhost/sitemap.xml');
    }

    public function test_public_profile_lists_services_with_local_business_schema_and_respects_visibility(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'studio-x', 'name' => 'Studio X', 'category' => 'nails', 'city' => 'Brno', 'country' => 'CZ', 'currency' => 'CZK', 'description' => 'Nechtové štúdio v centre.']);
        Tenancy::set($tenant);
        $category = Category::create(['name' => 'Nechty', 'city' => '']);
        Service::create(['category_id' => $category->id, 'name' => 'Gélové nechty', 'duration' => 90, 'break_time' => 0, 'price' => '890', 'city' => '']);
        Tenancy::forget();

        $this->get('/studio-x')->assertOk()
            ->assertSee('Studio X')->assertSee('Gélové nechty')->assertSee('890 CZK')
            ->assertSee('"@type":"NailSalon"', false)->assertSee('"@type":"ReserveAction"', false)
            ->assertSee('<meta name="robots" content="index, follow">', false)
            ->assertSee('href="http://localhost/studio-x/booking"', false)
            ->assertHeaderMissing('X-Robots-Tag');

        // The booking flow itself stays out of the index; the profile is canonical.
        $this->get('/studio-x/booking')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');

        $tenant->update(['is_public' => false]);
        $this->get('/studio-x')->assertOk()->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }
}
