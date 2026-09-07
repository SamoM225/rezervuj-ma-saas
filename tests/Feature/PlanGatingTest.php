<?php

namespace Tests\Feature;

use App\Models\BusinessSetting;
use App\Models\City;
use App\Models\Setting;
use App\Models\Tenant;
use App\Models\User;
use App\Support\Tenancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanGatingTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private User $owner;

    private string $p;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create(['slug' => 'free-salon']);
        Tenancy::set($this->tenant);
        $this->owner = User::factory()->create(['role' => 'superadmin', 'email_otp_enabled' => false]);
        BusinessSetting::set('default_language', 'sk', 'string');
        BusinessSetting::set('available_languages', ['sk'], 'json');
        City::create(['name' => 'Bratislava', 'address' => 'Hlavná 1']);
        $this->p = '/'.$this->tenant->slug;
        Tenancy::forget();
    }

    public function test_free_plan_keeps_one_language_standard_look_one_location_and_no_widget(): void
    {
        $this->actingAs($this->owner)->put($this->p.'/admin/business-settings', $this->bookingRules(['available_languages' => ['sk', 'cs', 'en'], 'language_switcher_enabled' => 1]))
            ->assertRedirect();
        Tenancy::set($this->tenant);
        $this->assertSame(['sk'], $this->languages());
        $this->assertFalse((bool) BusinessSetting::get('language_switcher_enabled'));

        $this->actingAs($this->owner)->post($this->p.'/admin/settings', $this->appearance(['main_accent' => '#123456', 'embed_allowed_origins' => 'https://example.test']))
            ->assertRedirect();
        $this->assertSame(Setting::DEFAULT_ACCENT, Setting::get('main_accent', Setting::DEFAULT_ACCENT));
        $this->assertSame('', (string) BusinessSetting::get('embed_allowed_origins', ''));

        $this->actingAs($this->owner)->put($this->p.'/admin/email-templates', ['config' => json_encode(['texts' => ['confirmed' => ['subject' => 'X']]])])
            ->assertRedirect()->assertSessionHas('error');
        $this->assertNull(BusinessSetting::get('email_template_config'));

        $this->actingAs($this->owner)->post($this->p.'/admin/cities', ['name' => 'Košice'])->assertRedirect()->assertSessionHas('error');
        $this->assertSame(1, City::count());

        $this->get($this->p.'/widget.js')->assertForbidden();
        $this->get($this->p.'/booking')->assertOk()->assertSee(__('widget.powered_by'));
        $this->actingAs($this->owner)->get($this->p.'/admin/settings')->assertOk()->assertSee(__('admin.pro.branding'));
    }

    public function test_pro_plan_unlocks_everything(): void
    {
        $this->tenant->update(['plan' => Tenant::PLAN_PRO, 'pro_until' => now()->addMonth()]);

        $this->actingAs($this->owner)->put($this->p.'/admin/business-settings', $this->bookingRules(['available_languages' => ['sk', 'cs', 'en'], 'language_switcher_enabled' => 1]))
            ->assertRedirect()->assertSessionHasNoErrors();
        Tenancy::set($this->tenant);
        $this->assertEqualsCanonicalizing(['sk', 'cs', 'en'], $this->languages());
        $this->assertTrue((bool) BusinessSetting::get('language_switcher_enabled'));

        $this->actingAs($this->owner)->post($this->p.'/admin/settings', $this->appearance(['main_accent' => '#123456', 'embed_allowed_origins' => 'https://example.test']))->assertRedirect();
        $this->assertSame('#123456', Setting::get('main_accent'));

        $this->actingAs($this->owner)->put($this->p.'/admin/email-templates', ['config' => json_encode(['texts' => ['confirmed' => ['subject' => 'X']]])])->assertRedirect()->assertSessionMissing('error');
        $this->assertNotNull(BusinessSetting::get('email_template_config'));

        $this->actingAs($this->owner)->post($this->p.'/admin/cities', ['name' => 'Košice'])->assertRedirect()->assertSessionMissing('error');
        $this->assertSame(2, City::count());

        $this->get($this->p.'/widget.js')->assertOk();
        $this->get($this->p.'/booking')->assertOk()->assertDontSee(__('widget.powered_by'));
    }

    public function test_staff_pick_their_own_admin_language_without_touching_the_booking_page(): void
    {
        $this->actingAs($this->owner)->get($this->p.'/admin/dashboard')->assertOk()->assertSee(__('ui.prehlad', [], 'sk'))->assertSee('<html lang="sk">', false);

        $this->actingAs($this->owner)->post($this->p.'/language/switch', ['locale' => 'en', 'admin' => 1])->assertRedirect();
        $this->actingAs($this->owner)->get($this->p.'/admin/dashboard')->assertOk()->assertSee('Overview')->assertSee('<html lang="en">', false)
            ->assertSee('window.__cal = {"today":"Today"', false);

        // The public booking page still follows the tenant's language, not the staff member's.
        $this->get($this->p.'/booking')->assertOk()->assertSee('<html lang="sk">', false);
    }

    /** @return array<int, string> */
    private function languages(): array
    {
        $value = BusinessSetting::get('available_languages');

        return array_values(is_array($value) ? $value : (array) json_decode((string) $value, true));
    }

    /** @return array<string, mixed> */
    private function bookingRules(array $override = []): array
    {
        return array_replace([
            'business_start_time' => '08:00', 'business_end_time' => '18:00', 'booking_advance_days' => 30, 'booking_advance_hours' => 2,
            'service_buffer_minutes' => 0, 'slot_hold_minutes' => 5, 'reminder_hours_before' => 24, 'default_language' => 'sk',
        ], $override);
    }

    /** @return array<string, mixed> */
    private function appearance(array $override = []): array
    {
        return array_replace([
            'main_accent' => Setting::DEFAULT_ACCENT, 'text_color' => '#111111', 'booking_bg_color' => '#ffffff', 'business_name' => 'Free Salon',
            'logo_width' => 180, 'hero_overlay_color' => '#1f1a14', 'hero_overlay_opacity' => 0.5,
        ], $override);
    }
}
