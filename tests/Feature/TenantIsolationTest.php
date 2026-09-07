<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\City;
use App\Models\Service;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WorkerAvailability;
use App\Support\PlanLimits;
use App\Support\Tenancy;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $a;

    private Tenant $b;

    protected function setUp(): void
    {
        parent::setUp();
        config(['antibot.enabled' => false]);
        Mail::fake();
        $this->a = $this->makeTenant('salon-a');
        $this->b = $this->makeTenant('salon-b');
        Tenancy::forget();
    }

    public function test_unknown_reserved_and_suspended_slugs_are_not_served(): void
    {
        $this->get('/nobody-here/booking')->assertNotFound();
        $this->get('/admin/booking')->assertNotFound();

        $this->a->update(['status' => Tenant::STATUS_SUSPENDED]);
        $this->get('/salon-a/booking')->assertForbidden();
    }

    public function test_each_tenant_sees_only_its_own_catalogue_and_settings(): void
    {
        $this->getJson('/salon-a/web/categories')->assertOk()->assertJsonCount(1)->assertJsonPath('0.name', 'Kategória salon-a');
        $this->getJson('/salon-b/web/categories')->assertOk()->assertJsonCount(1)->assertJsonPath('0.name', 'Kategória salon-b');

        Tenancy::set($this->a);
        BusinessSetting::set('business_name', 'Salon A', 'string');
        Tenancy::set($this->b);
        $this->assertNull(BusinessSetting::get('business_name'), 'settings cache and query are tenant-scoped');
        Tenancy::set($this->a);
        $this->assertSame('Salon A', BusinessSetting::get('business_name'));
    }

    public function test_a_booking_cannot_reference_another_tenants_worker_or_service(): void
    {
        [$workerB, $serviceB, $cityB] = $this->fixtures($this->b);
        Tenancy::forget();

        $this->postJson('/salon-a/booking', [
            'date' => Carbon::tomorrow()->toDateString(), 'time' => '10:00',
            'worker_id' => $workerB->id, 'service_id' => $serviceB->id, 'city_id' => $cityB->id,
            'name' => 'X', 'email' => 'x@example.test', 'phone' => '+421900000000', 'gdpr' => true,
        ])->assertUnprocessable()->assertJsonValidationErrors(['worker_id', 'service_id', 'city_id']);

        $this->assertSame(0, Booking::acrossTenants()->count());
    }

    public function test_staff_session_from_one_tenant_is_rejected_on_another_tenants_admin(): void
    {
        Tenancy::set($this->a);
        $adminA = User::factory()->admin()->create();
        Tenancy::forget();

        $this->actingAs($adminA)->get('/salon-a/admin/dashboard')->assertOk();
        $this->actingAs($adminA)->get('/salon-b/admin/dashboard')->assertRedirect('/salon-b/login');
    }

    public function test_free_plan_stops_at_fifty_bookings_a_month_and_pro_does_not(): void
    {
        [$worker, $service, $city] = $this->fixtures($this->a);
        Tenancy::set($this->a);
        // Past slots so they count towards this month's quota without colliding with tomorrow's booking.
        Booking::factory()->count(50)->create(['user_id' => $worker->id, 'service_id' => $service->id, 'city_id' => $city->id, 'status' => 'confirmed', 'date' => Carbon::yesterday()->toDateString(), 'start_time' => '08:00:00', 'end_time' => '08:30:00']);
        $this->assertSame(0, PlanLimits::remainingBookings($this->a));
        Tenancy::forget();

        $payload = [
            'date' => Carbon::tomorrow()->toDateString(), 'time' => '10:00',
            'worker_id' => $worker->id, 'service_id' => $service->id, 'city_id' => $city->id,
            'name' => 'Jana', 'email' => 'jana@example.test', 'phone' => '+421900123456', 'gdpr' => true,
        ];
        $this->postJson('/salon-a/booking', $payload)->assertUnprocessable()->assertJsonValidationErrors(['date']);

        $this->a->update(['plan' => Tenant::PLAN_PRO, 'pro_until' => now()->addMonth()]);
        $this->assertNull(PlanLimits::remainingBookings($this->a->fresh()));
        $this->postJson('/salon-a/booking', $payload)->assertCreated();
    }

    private function makeTenant(string $slug): Tenant
    {
        $tenant = Tenant::factory()->create(['slug' => $slug]);
        Tenancy::set($tenant);
        BusinessSetting::set('allow_online_booking', true, 'boolean');
        BusinessSetting::set('send_email_notifications', false, 'boolean');
        BusinessSetting::set('booking_advance_hours', 1, 'integer');
        BusinessSetting::set('service_buffer_minutes', 0, 'integer');
        $city = City::create(['name' => 'Mesto', 'address' => 'Ulica 1']);
        $category = Category::create(['name' => "Kategória {$slug}", 'city' => '']);
        $category->cities()->attach($city);

        return $tenant;
    }

    /** @return array{User, Service, City} */
    private function fixtures(Tenant $tenant): array
    {
        Tenancy::set($tenant);
        $city = City::firstOrFail();
        $category = Category::firstOrFail();
        $service = Service::create(['category_id' => $category->id, 'name' => 'Služba', 'duration' => 60, 'break_time' => 0, 'price' => '30', 'city' => '']);
        $worker = User::factory()->worker()->create(['city_id' => $city->id]);
        $worker->services()->attach($service);
        $worker->categories()->attach($category);
        WorkerAvailability::create([
            'user_id' => $worker->id, 'start_date' => Carbon::yesterday()->toDateString(), 'end_date' => Carbon::tomorrow()->addMonth()->toDateString(),
            'days_of_week' => [0, 1, 2, 3, 4, 5, 6], 'start_time' => '08:00', 'end_time' => '18:00', 'is_active' => true,
        ]);

        return [$worker, $service, $city];
    }
}
