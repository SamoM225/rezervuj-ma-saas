<?php

namespace Tests\Feature;

use App\Mail\BookingNotification;
use App\Models\Booking;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\City;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WorkerAvailability;
use App\Support\SlotHolds;
use App\Support\Tenancy;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class BookingAndAuthorizationFlowTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private string $p;

    private User $worker;

    private Service $service;

    private City $city;

    private string $date;

    protected function setUp(): void
    {
        parent::setUp();
        config(['antibot.enabled' => false, 'mail.default' => 'array']);
        Mail::fake();
        $this->tenant = Tenant::factory()->create(['slug' => 'demo-studio']);
        Tenancy::set($this->tenant);
        $this->p = '/'.$this->tenant->slug;
        BusinessSetting::set('allow_online_booking', true, 'boolean');
        BusinessSetting::set('send_email_notifications', false, 'boolean');
        BusinessSetting::set('booking_advance_hours', 1, 'integer');
        BusinessSetting::set('booking_advance_days', 60, 'integer');
        BusinessSetting::set('booking_advance_weeks', 0, 'integer');
        BusinessSetting::set('booking_advance_months', 0, 'integer');
        BusinessSetting::set('service_buffer_minutes', 0, 'integer');

        $this->city = City::create(['name' => 'Bratislava', 'address' => 'Test 1']);
        $category = Category::create(['name' => 'Kozmetika', 'city' => '']);
        $category->cities()->attach($this->city);
        $this->service = Service::create(['category_id' => $category->id, 'name' => 'Ošetrenie', 'duration' => 60, 'break_time' => 0, 'price' => '45', 'city' => '']);
        $this->worker = User::factory()->worker()->create(['city_id' => $this->city->id]);
        $this->worker->services()->attach($this->service);
        $this->worker->categories()->attach($category);
        $this->date = Carbon::tomorrow()->toDateString();
        WorkerAvailability::create([
            'user_id' => $this->worker->id, 'start_date' => Carbon::yesterday()->toDateString(), 'end_date' => Carbon::tomorrow()->addMonth()->toDateString(),
            'days_of_week' => [0, 1, 2, 3, 4, 5, 6], 'start_time' => '08:00', 'end_time' => '18:00', 'is_active' => true,
        ]);
    }

    public function test_public_customer_can_create_a_valid_booking_and_signed_ics_protects_personal_data(): void
    {
        $this->postJson($this->p.'/booking', $this->bookingPayload())->assertCreated()->assertJsonPath('booking.status', 'confirmed');
        $booking = Booking::firstOrFail();
        $this->assertSame('11:00:00', $booking->end_time);
        $this->get($this->p.'/booking/'.$booking->id.'/calendar.ics')->assertForbidden();
        $this->get(URL::temporarySignedRoute('booking.ics', now()->addMinutes(5), ['booking' => $booking->id]))->assertOk()->assertHeader('content-type', 'text/calendar; charset=utf-8');
    }

    public function test_public_booking_rejects_conflicts_and_manual_blocks(): void
    {
        $this->postJson($this->p.'/booking', $this->bookingPayload())->assertCreated();
        $this->postJson($this->p.'/booking', $this->bookingPayload(['email' => 'other@example.test']))->assertUnprocessable();

        $date = Carbon::tomorrow()->addDay()->toDateString();
        Schedule::create(['user_id' => $this->worker->id, 'date' => $date, 'start_time' => '10:00:00', 'end_time' => '12:00:00', 'status' => 'unavailable', 'type' => 'manual-block', 'city' => 'Bratislava']);
        $this->postJson($this->p.'/booking', $this->bookingPayload(['date' => $date, 'time' => '10:00', 'email' => 'blocked@example.test']))->assertUnprocessable();
    }

    public function test_fixed_public_slots_and_worker_daily_booking_limit_are_enforced(): void
    {
        BusinessSetting::set('enforce_fixed_start_times', true, 'boolean');
        BusinessSetting::set('max_bookings_per_worker_per_day', 1, 'integer');
        $this->service->update(['break_time' => 15]);

        $this->postJson($this->p.'/booking', $this->bookingPayload(['time' => '08:45']))
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Vyberte jeden z ponúknutých pevných časov rezervácie.');

        $this->postJson($this->p.'/booking', $this->bookingPayload(['time' => '08:00']))
            ->assertCreated();

        $this->postJson($this->p.'/booking', $this->bookingPayload([
            'time' => '09:15',
            'email' => 'second-customer@example.test',
            'phone' => '+421900654321',
        ]))->assertUnprocessable();
    }

    public function test_admin_can_add_a_worker_and_booking_but_cannot_create_privileged_account(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get($this->p.'/admin/dashboard')->assertOk();
        $this->actingAs($admin)->get($this->p.'/admin/workers')->assertOk();
        $this->actingAs($admin)->post($this->p.'/admin/workers', [
            'name' => 'Nový pracovník', 'email' => 'new.worker@example.test', 'password' => 'very-long-safe-password', 'role' => 'worker', 'city_id' => $this->city->id,
        ])->assertRedirect(route('admin.workers'));
        $this->assertDatabaseHas('users', ['email' => 'new.worker@example.test', 'role' => 'worker']);

        $this->actingAs($admin)->post($this->p.'/admin/workers', [
            'name' => 'Nope', 'email' => 'nope@example.test', 'password' => 'very-long-safe-password', 'role' => 'admin',
        ])->assertForbidden();

        $this->actingAs($admin)->post($this->p.'/admin/bookings', $this->bookingPayload(['email' => 'admin-created@example.test']))->assertRedirect();
        $this->assertDatabaseHas('bookings', ['customer_email' => 'admin-created@example.test']);
    }

    public function test_deleted_booking_leaves_the_calendar_but_stays_in_customer_history(): void
    {
        $admin = User::factory()->admin()->create();
        $this->postJson($this->p.'/booking', $this->bookingPayload())->assertCreated();
        $booking = Booking::firstOrFail();

        $this->actingAs($admin)->deleteJson($this->p.'/admin/bookings/'.$booking->id)->assertNoContent();
        $this->assertSoftDeleted('bookings', ['id' => $booking->id]);

        $feed = $this->actingAs($admin)->getJson($this->p.'/admin/bookings/calendar-data?start='.$this->date.'&end='.$this->date)->assertOk();
        $this->assertEmpty($feed->json('bookings'));
        // The slot is free again for another customer.
        $this->postJson($this->p.'/booking', $this->bookingPayload(['email' => 'next@example.test']))->assertCreated();
        // History keeps the removed appointment.
        $this->actingAs($admin)->getJson($this->p.'/admin/customers/'.urlencode($booking->customer_email))->assertOk()
            ->assertJsonFragment(['id' => $booking->id, 'status' => 'deleted']);
    }

    public function test_any_worker_booking_merges_availability_and_assigns_a_specific_worker(): void
    {
        $secondWorker = User::factory()->worker()->create(['city_id' => $this->city->id]);
        $secondWorker->services()->attach($this->service);
        $secondWorker->categories()->attach($this->worker->categories()->first());
        WorkerAvailability::create([
            'user_id' => $secondWorker->id, 'start_date' => Carbon::yesterday()->toDateString(), 'end_date' => Carbon::tomorrow()->addMonth()->toDateString(),
            'days_of_week' => [0, 1, 2, 3, 4, 5, 6], 'start_time' => '08:00', 'end_time' => '18:00', 'is_active' => true,
        ]);
        $category = $this->worker->categories()->first();

        $datesResponse = $this->getJson($this->p.'/web/dates/any?service_id='.$this->service->id.'&category_id='.$category->id)->assertOk();
        $this->assertContains($this->date, $datesResponse->json('availableDates'));

        $timesResponse = $this->getJson($this->p.'/web/times/any/'.$this->date.'?service_id='.$this->service->id.'&category_id='.$category->id)->assertOk();
        $slots = $timesResponse->json('timeSlots');
        $this->assertNotEmpty($slots);
        $this->assertContains($slots[0]['worker_id'], [$this->worker->id, $secondWorker->id]);

        // Booking one worker's slot leaves the other worker still offering it under "any".
        Booking::create([
            'user_id' => $this->worker->id, 'service_id' => $this->service->id, 'city_id' => $this->city->id,
            'customer_name' => 'Taken', 'customer_email' => 'taken@example.test', 'customer_phone' => '+421900000009',
            'city' => $this->city->name, 'date' => $this->date, 'start_time' => $slots[0]['start_time'].':00', 'end_time' => $slots[0]['end_time'].':00', 'status' => 'confirmed',
        ]);
        $afterBooking = $this->getJson($this->p.'/web/times/any/'.$this->date.'?service_id='.$this->service->id.'&category_id='.$category->id)->assertOk();
        $assignedNow = collect($afterBooking->json('timeSlots'))->firstWhere('start_time', $slots[0]['start_time']);
        $this->assertSame($secondWorker->id, $assignedNow['worker_id']);
    }

    public function test_worker_cannot_access_admin_management(): void
    {
        $this->actingAs($this->worker)->get($this->p.'/admin/workers')->assertForbidden();
        $this->actingAs($this->worker)->post($this->p.'/admin/workers', [])->assertForbidden();
    }

    public function test_admin_can_log_in_and_customer_can_cancel_only_via_signed_link(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class);
        // E-mail second factor is covered in SignupAndAuthTest; this account opts out.
        $admin = User::factory()->admin()->create(['email' => 'admin@login.test', 'password' => 'very-long-safe-password', 'email_otp_enabled' => false]);
        $this->post($this->p.'/login', ['email' => $admin->email, 'password' => 'very-long-safe-password'])->assertRedirect($this->p.'/admin/dashboard');
        $this->assertAuthenticatedAs($admin);

        $this->postJson($this->p.'/booking', $this->bookingPayload())->assertCreated();
        $booking = Booking::firstOrFail();
        $this->post($this->p.'/booking/'.$booking->id.'/cancel')->assertForbidden();
        $this->post(URL::temporarySignedRoute('booking.cancel', now()->addMinutes(5), ['booking' => $booking->id]))->assertRedirect();
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'cancelled']);
    }

    public function test_calendar_feed_is_scoped_to_worker_and_worker_can_manage_own_blocks(): void
    {
        $otherWorker = User::factory()->worker()->create(['city_id' => $this->city->id]);
        $otherWorker->services()->attach($this->service);

        Booking::create([
            'user_id' => $this->worker->id,
            'service_id' => $this->service->id,
            'city_id' => $this->city->id,
            'customer_name' => 'Visible customer',
            'customer_email' => 'visible@example.test',
            'customer_phone' => '+421900111111',
            'city' => $this->city->name,
            'date' => $this->date,
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'status' => 'confirmed',
        ]);
        Booking::create([
            'user_id' => $otherWorker->id,
            'service_id' => $this->service->id,
            'city_id' => $this->city->id,
            'customer_name' => 'Hidden customer',
            'customer_email' => 'hidden@example.test',
            'customer_phone' => '+421900222222',
            'city' => $this->city->name,
            'date' => $this->date,
            'start_time' => '12:00:00',
            'end_time' => '13:00:00',
            'status' => 'confirmed',
        ]);
        Schedule::create([
            'user_id' => $this->worker->id,
            'date' => $this->date,
            'start_time' => '14:00:00',
            'end_time' => '15:00:00',
            'status' => 'unavailable',
            'type' => 'manual-block',
            'title' => 'Private time',
            'city' => $this->city->name,
        ]);

        $this->actingAs($this->worker)
            ->getJson(route('worker.calendar.feed', ['start' => $this->date, 'end' => $this->date]))
            ->assertOk()
            ->assertJsonCount(1, 'bookings')
            ->assertJsonCount(1, 'blocks')
            ->assertJsonPath('bookings.0.customer_name', 'Visible customer');

        $this->actingAs($this->worker)
            ->postJson(route('worker.calendar.blocks.store'), [
                'worker_id' => $this->worker->id,
                'date' => $this->date,
                'start_time' => '16:00',
                'end_time' => '17:00',
                'title' => 'Own block',
            ])
            ->assertCreated();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)
            ->get(route('admin.bookings.index'))
            ->assertOk()
            ->assertSee('booking-calendar-app');
        $this->actingAs($admin)
            ->getJson(route('admin.bookings.calendar-data', ['start' => $this->date, 'end' => $this->date]))
            ->assertOk()
            ->assertJsonCount(2, 'bookings')
            ->assertJsonCount(2, 'blocks');

        $this->actingAs($this->worker)
            ->get(route('worker.calendar'))
            ->assertOk()
            ->assertSee('booking-calendar-app');
    }

    public function test_demo_tenant_seeder_is_idempotent_and_scoped_to_its_tenant(): void
    {
        Tenancy::forget();
        $this->artisan('db:seed')->assertExitCode(0);
        $this->artisan('db:seed')->assertExitCode(0);

        $demo = Tenant::where('slug', 'demo')->firstOrFail();
        $this->assertDatabaseHas('users', ['email' => 'admin@example.test', 'role' => 'superadmin', 'tenant_id' => $demo->id]);
        $this->assertSame($demo->owner_user_id, User::acrossTenants()->where('email', 'admin@example.test')->value('id'));
        $this->assertSame(1, Service::acrossTenants()->where('tenant_id', $demo->id)->where('name', 'Manikúra')->count(), 'services are not duplicated on re-run');
        $this->assertSame(0, Service::acrossTenants()->where('tenant_id', $this->tenant->id)->where('name', 'Manikúra')->count(), 'seeded data does not leak into other tenants');

        // The seeded tenant's booking page works and only shows its own catalogue.
        $this->get('/demo/booking')->assertOk();
        $this->getJson('/demo/web/categories')->assertOk()->assertJsonCount(3)->assertJsonFragment(['name' => 'Nechty']);
        $this->getJson($this->p.'/web/categories')->assertOk()->assertJsonCount(1)->assertJsonMissing(['name' => 'Nechty']);
    }

    public function test_public_flow_endpoints_return_json_and_a_hold_reports_its_expiry(): void
    {
        $this->getJson($this->p.'/web/categories/'.$this->city->id)->assertOk()->assertJsonPath('0.name', 'Kozmetika');
        $this->getJson($this->p.'/web/procedures/'.$this->service->category_id)->assertOk()->assertJsonPath('0.id', $this->service->id)->assertJsonPath('0.price_label', '45,00 €');
        $this->getJson($this->p.'/web/workers/'.$this->service->category_id.'?service_id='.$this->service->id.'&city_id='.$this->city->id)
            ->assertOk()->assertJsonCount(1)->assertJsonPath('0.id', $this->worker->id);
        $this->getJson($this->p.'/web/times/'.$this->worker->id.'/'.$this->date.'?service_id='.$this->service->id)
            ->assertOk()->assertJsonPath('timeSlots.0.start_time', '08:00');

        $this->postJson($this->p.'/web/hold', ['worker_id' => $this->worker->id, 'service_id' => $this->service->id, 'date' => $this->date, 'start_time' => '10:00'])
            ->assertOk()->assertJsonStructure(['expires_at', 'hold_seconds']);

        // A different visitor (new session) cannot take the held slot…
        $this->flushSession();
        $this->postJson($this->p.'/booking', $this->bookingPayload(['email' => 'other@example.test']))->assertUnprocessable();

        // …while the visitor who holds it books it, and the public response carries
        // the signed confirmation URL to redirect to.
        $this->flushSession();
        $this->postJson($this->p.'/web/hold', ['worker_id' => $this->worker->id, 'service_id' => $this->service->id, 'date' => $this->date, 'start_time' => '12:00'])->assertOk();
        $this->postJson($this->p.'/booking', $this->bookingPayload(['time' => '12:00']))->assertCreated()->assertJsonStructure(['redirect']);
        $this->assertFalse(SlotHolds::conflicts($this->worker->id, $this->date, '12:00:00', '13:00:00'), 'the visitor\'s own hold is released once they book');
        $this->assertTrue(SlotHolds::conflicts($this->worker->id, $this->date, '10:00:00', '11:00:00'), 'the first visitor\'s hold is still active');
    }

    public function test_worker_can_book_and_move_only_their_own_appointments(): void
    {
        $otherWorker = User::factory()->worker()->create(['city_id' => $this->city->id]);
        $otherWorker->services()->attach($this->service);
        WorkerAvailability::create([
            'user_id' => $otherWorker->id, 'start_date' => Carbon::yesterday()->toDateString(), 'end_date' => null,
            'days_of_week' => [0, 1, 2, 3, 4, 5, 6], 'start_time' => '08:00', 'end_time' => '18:00', 'is_active' => true,
        ]);

        $this->actingAs($this->worker)
            ->postJson(route('worker.bookings.store'), $this->bookingPayload(['gdpr' => null]))
            ->assertCreated();
        $own = Booking::where('user_id', $this->worker->id)->firstOrFail();

        $this->actingAs($this->worker)
            ->postJson(route('worker.bookings.store'), $this->bookingPayload(['worker_id' => $otherWorker->id, 'time' => '12:00', 'email' => 'x@example.test', 'gdpr' => null]))
            ->assertForbidden();

        $this->actingAs($this->worker)
            ->patchJson(route('worker.bookings.reschedule', $own), ['date' => $this->date, 'start_time' => '14:00'])
            ->assertOk()->assertJsonPath('booking.start_time', '14:00:00');

        $foreign = Booking::create([
            'user_id' => $otherWorker->id, 'service_id' => $this->service->id, 'city_id' => $this->city->id,
            'customer_name' => 'Cudzí', 'customer_email' => 'cudzi@example.test', 'customer_phone' => '+421900000000',
            'city' => $this->city->name, 'date' => $this->date, 'start_time' => '09:00:00', 'end_time' => '10:00:00', 'status' => 'confirmed',
        ]);
        $this->actingAs($this->worker)
            ->patchJson(route('worker.bookings.reschedule', $foreign), ['date' => $this->date, 'start_time' => '15:00'])
            ->assertForbidden();
        $this->actingAs($this->worker)
            ->patchJson(route('worker.bookings.update-status', $foreign), ['status' => 'cancelled'])
            ->assertForbidden();
    }

    public function test_cancellation_sends_the_templated_email_and_admin_pages_render(): void
    {
        BusinessSetting::set('send_email_notifications', true, 'boolean');
        $this->postJson($this->p.'/booking', $this->bookingPayload())->assertCreated();
        $booking = Booking::firstOrFail();

        $admin = User::factory()->create(['role' => 'superadmin']);
        Mail::fake();
        $this->actingAs($admin)->patchJson(route('admin.bookings.update-status', $booking), ['status' => 'cancelled'])->assertOk();
        Mail::assertSent(BookingNotification::class, fn (BookingNotification $mail) => $mail->type === 'cancelled'
            && $mail->bookingId === $booking->id
            && str_contains($mail->subjectLine, 'Zrušenie')
            && str_contains($mail->body, 'Jana Testová'));

        foreach (['admin.dashboard', 'admin.bookings.index', 'admin.workers', 'admin.workers.create', 'admin.services', 'admin.services.create', 'admin.categories', 'admin.cities.index', 'admin.cities.create', 'admin.customers.index', 'admin.blacklist.index', 'admin.blacklist.create', 'admin.settings', 'admin.business-settings', 'admin.email-templates.index', 'account.2fa.show'] as $route) {
            $this->actingAs($admin)->get(route($route))->assertOk();
        }
        $this->actingAs($admin)->get(route('admin.workers.edit', $this->worker))->assertOk();
        $this->actingAs($admin)->get(route('admin.workers.availability', $this->worker))->assertOk();

        foreach (['worker.dashboard', 'worker.calendar', 'worker.availability'] as $route) {
            $this->actingAs($this->worker)->get(route($route))->assertOk();
        }

        $this->get($this->p.'/booking')->assertOk()->assertSee('step-datetime', false)->assertSee('cookie-notice', false)->assertSee('bk-topbar', false);
        $this->get(route('privacy'))->assertOk()->assertSee('bk-topbar', false);
        $this->get(route('terms'))->assertOk()->assertSee('Podmienky online rezervácie');
        $this->get(route('login'))->assertOk();
        $this->get($this->p.'/neexistujuca-stranka')->assertNotFound()->assertSee('Stránka sa nenašla');

        // Website widget (Pro): script is public, framing only for the configured websites, staff pages never.
        $this->tenant->update(['plan' => Tenant::PLAN_PRO, 'pro_until' => now()->addMonth()]);
        $this->get($this->p.'/widget.js')->assertOk()->assertHeader('content-type', 'application/javascript; charset=utf-8')->assertSee('embed=1', false);
        // The site stays out of search engines while it's a single client's booking page.
        $this->get($this->p.'/booking')->assertHeader('X-Robots-Tag', 'noindex, nofollow')->assertSee('name="robots" content="noindex, nofollow"', false);
        $this->get($this->p.'/booking')->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        BusinessSetting::set('embed_allowed_origins', 'https://www.example-salon.sk, javascript:alert(1)', 'string');
        $this->get($this->p.'/booking?embed=1')->assertOk()->assertHeaderMissing('X-Frame-Options')->assertDontSee('bk-topbar', false)
            ->assertHeader('Content-Security-Policy-Report-Only');
        $this->assertStringContainsString("frame-ancestors 'self' https://www.example-salon.sk", $this->get($this->p.'/booking')->headers->get('Content-Security-Policy-Report-Only'));
        $this->assertStringNotContainsString('javascript', $this->get($this->p.'/booking')->headers->get('Content-Security-Policy-Report-Only'));
        $this->get(route('login'))->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }

    private function bookingPayload(array $override = []): array
    {
        return array_replace([
            'date' => $this->date, 'time' => '10:00', 'worker_id' => $this->worker->id, 'service_id' => $this->service->id, 'city_id' => $this->city->id,
            'name' => 'Jana Testová', 'email' => 'jana@example.test', 'phone' => '+421900123456', 'gdpr' => true,
        ], $override);
    }
}
