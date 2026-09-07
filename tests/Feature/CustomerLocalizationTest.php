<?php

namespace Tests\Feature;

use App\Mail\BookingNotification;
use App\Models\Booking;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\City;
use App\Models\Service;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WorkerAvailability;
use App\Support\BookingMailer;
use App\Support\Tenancy;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class CustomerLocalizationTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private string $p;

    private User $worker;

    private Service $service;

    private City $city;

    protected function setUp(): void
    {
        parent::setUp();
        config(['antibot.enabled' => false]);
        Mail::fake();
        $this->tenant = Tenant::factory()->create(['slug' => 'studio-cz', 'name' => 'Studio Praha', 'country' => 'CZ', 'currency' => 'CZK', 'locale' => 'cs', 'email' => 'studio@example.test']);
        Tenancy::set($this->tenant);
        $this->p = '/'.$this->tenant->slug;
        BusinessSetting::set('default_language', 'cs', 'string');
        BusinessSetting::set('available_languages', ['sk', 'cs', 'en'], 'json');
        BusinessSetting::set('send_email_notifications', true, 'boolean');
        BusinessSetting::set('booking_advance_hours', 1, 'integer');
        BusinessSetting::set('booking_advance_days', 60, 'integer');
        BusinessSetting::set('service_buffer_minutes', 0, 'integer');

        $this->city = City::create(['name' => 'Praha', 'address' => 'Náměstí 1']);
        $category = Category::create(['name' => 'Nehty', 'city' => '']);
        $category->cities()->attach($this->city);
        $this->service = Service::create(['category_id' => $category->id, 'name' => 'Gelové nehty', 'duration' => 60, 'break_time' => 0, 'price' => '890', 'city' => '']);
        $this->worker = User::factory()->worker()->create(['city_id' => $this->city->id]);
        $this->worker->services()->attach($this->service);
        $this->worker->categories()->attach($category);
        WorkerAvailability::create([
            'user_id' => $this->worker->id, 'start_date' => Carbon::yesterday()->toDateString(), 'end_date' => Carbon::tomorrow()->addMonth()->toDateString(),
            'days_of_week' => [0, 1, 2, 3, 4, 5, 6], 'start_time' => '08:00', 'end_time' => '18:00', 'is_active' => true,
        ]);
    }

    public function test_booking_remembers_the_customer_language_and_emails_follow_it(): void
    {
        $this->postJson($this->p.'/booking', $this->payload() + ['locale' => 'en'])->assertCreated();

        $booking = Booking::firstOrFail();
        $this->assertSame('en', $booking->locale);
        Mail::assertSent(BookingNotification::class, fn (BookingNotification $mail) => str_starts_with($mail->subjectLine, 'Booking confirmation')
            && str_contains($mail->body, '890 Kč') && str_contains($mail->body, 'Booking details'));

        // The tenant later cancels from a Slovak admin session: the customer still gets English.
        app()->setLocale('sk');
        [$subject, $html] = BookingMailer::render($booking, BookingMailer::CANCELLED);
        $this->assertStringStartsWith('Booking cancelled', $subject);
        $this->assertStringContainsString('Guest details', $html);
        $this->assertSame('sk', app()->getLocale());
    }

    public function test_tenant_texts_apply_only_to_the_tenant_language(): void
    {
        BusinessSetting::set('email_template_config', ['texts' => ['confirmed' => ['subject' => 'Vlastní předmět {{service_name}}']], 'blocks' => ['location' => ['enabled' => false, 'title' => 'Kde jsme']]], 'json');

        $czech = Booking::factory()->create(['user_id' => $this->worker->id, 'service_id' => $this->service->id, 'locale' => 'cs', 'customer_email' => 'cz@example.test']);
        $english = Booking::factory()->create(['user_id' => $this->worker->id, 'service_id' => $this->service->id, 'locale' => 'en', 'customer_email' => 'en@example.test']);

        [$subject] = BookingMailer::render($czech, BookingMailer::CONFIRMED);
        $this->assertSame('Vlastní předmět Gelové nehty', $subject);

        [$subject, $html] = BookingMailer::render($english, BookingMailer::CONFIRMED);
        $this->assertSame('Booking confirmation – Gelové nehty', $subject);
        $this->assertStringNotContainsString('Where to find us', $html, 'disabled blocks stay disabled in every language');
    }

    public function test_confirmation_and_cancellation_pages_speak_the_visitor_language(): void
    {
        $booking = Booking::factory()->create(['user_id' => $this->worker->id, 'service_id' => $this->service->id, 'locale' => 'en', 'status' => 'confirmed', 'date' => Carbon::tomorrow()->addDays(3)->toDateString()]);

        // Signed URLs cannot carry a ?locale switch, so the language comes from the session.
        $this->withSession(['locale' => 'en'])->get(URL::temporarySignedRoute('booking.confirmation', now()->addDay(), ['booking' => $booking->id]))
            ->assertOk()->assertSee('Your appointment is booked')->assertSee('890 Kč')->assertSee('<html lang="en">', false);

        $this->withSession(['locale' => 'cs'])->get(URL::temporarySignedRoute('booking.cancel.show', now()->addDay(), ['booking' => $booking->id]))
            ->assertOk()->assertSee('Zrušit tuto rezervaci?');
    }

    public function test_tenant_legal_pages_are_generated_from_tenant_data_in_each_language(): void
    {
        $this->get($this->p.'/ochrana-osobnych-udajov?locale=cs')->assertOk()
            ->assertSee('Informace o zpracování osobních údajů')->assertSee('Studio Praha')->assertSee('studio@example.test')
            ->assertSee('Úřad pro ochranu osobních údajů');

        $this->get($this->p.'/podmienky-rezervacie?locale=en')->assertOk()
            ->assertSee('Online booking terms')->assertSee('Studio Praha')->assertSee('Náměstí 1')->assertSee('Česká obchodní inspekce');

        $this->get($this->p.'/podmienky-rezervacie?locale=sk')->assertOk()->assertSee('Podmienky online rezervácie');
    }

    /** @return array<string, mixed> */
    private function payload(): array
    {
        return [
            'date' => Carbon::tomorrow()->toDateString(), 'time' => '10:00', 'worker_id' => $this->worker->id, 'service_id' => $this->service->id, 'city_id' => $this->city->id,
            'name' => 'Jane Tester', 'email' => 'jane@example.test', 'phone' => '+420600123456', 'gdpr' => true,
        ];
    }
}
