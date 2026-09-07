<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Category;
use App\Models\City;
use App\Models\Service;
use App\Models\Tenant;
use App\Models\User;
use App\Support\Tenancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GdprToolsTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private User $owner;

    private Booking $booking;

    private string $p;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create(['slug' => 'gdpr-salon']);
        Tenancy::set($this->tenant);
        $this->owner = User::factory()->create(['role' => 'superadmin', 'email_otp_enabled' => false, 'password' => Hash::make('Owner-Password-123!')]);
        $worker = User::factory()->worker()->create();
        $city = City::create(['name' => 'Nitra', 'address' => 'Štefánikova 1']);
        $category = Category::create(['name' => 'Kozmetika', 'city' => '']);
        $service = Service::create(['category_id' => $category->id, 'name' => 'Ošetrenie', 'duration' => 60, 'break_time' => 0, 'price' => '45', 'city' => '']);
        $this->booking = Booking::factory()->create(['user_id' => $worker->id, 'service_id' => $service->id, 'city_id' => $city->id, 'customer_name' => 'Eva Príkladová', 'customer_email' => 'eva@example.test', 'customer_phone' => '+421900111222', 'notes' => 'alergia na latex']);
        $this->p = '/'.$this->tenant->slug;
        Tenancy::forget();
    }

    public function test_customers_can_be_exported_and_a_customer_can_be_erased(): void
    {
        $csv = $this->actingAs($this->owner)->get($this->p.'/admin/customers/export')->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=utf-8')->streamedContent();
        $this->assertStringContainsString('eva@example.test', $csv);
        $this->assertStringContainsString('Eva Príkladová', $csv);

        $this->actingAs($this->owner)->delete($this->p.'/admin/customers/'.urlencode('eva@example.test'))->assertRedirect()->assertSessionHas('success');

        $booking = $this->booking->fresh();
        $this->assertStringNotContainsString('eva@', $booking->customer_email);
        $this->assertSame(__('ui.anonymized_customer'), $booking->customer_name);
        $this->assertSame('', $booking->customer_phone);
        $this->assertNull($booking->notes);
        $this->assertSame($booking->date, $this->booking->date, 'the appointment itself stays for statistics');
    }

    public function test_owner_can_export_everything_and_delete_the_account_which_is_purged_after_the_grace_period(): void
    {
        $json = json_decode($this->actingAs($this->owner)->get($this->p.'/admin/account/export')->assertOk()->streamedContent(), true);
        $this->assertSame('gdpr-salon', $json['tenant']['slug']);
        $this->assertCount(1, $json['bookings']);
        $this->assertCount(1, $json['services']);
        $this->assertArrayNotHasKey('password', $json['team'][0]);

        // Wrong password or slug: nothing happens.
        $this->actingAs($this->owner)->delete($this->p.'/admin/account', ['password' => 'nope', 'confirm_slug' => 'gdpr-salon'])->assertSessionHasErrors('password');
        $this->actingAs($this->owner)->delete($this->p.'/admin/account', ['password' => 'Owner-Password-123!', 'confirm_slug' => 'other'])->assertSessionHasErrors('confirm_slug');
        $this->assertSame(Tenant::STATUS_ACTIVE, $this->tenant->fresh()->status);

        $this->actingAs($this->owner)->delete($this->p.'/admin/account', ['password' => 'Owner-Password-123!', 'confirm_slug' => 'gdpr-salon'])->assertRedirect('http://localhost');
        $this->assertSame(Tenant::STATUS_DELETED, $this->tenant->fresh()->status);
        $this->get($this->p.'/booking')->assertNotFound();
        $this->get($this->p)->assertNotFound();

        $this->artisan('tenants:purge-deleted')->assertSuccessful();
        $this->assertDatabaseHas('tenants', ['slug' => 'gdpr-salon']);

        $this->travel(31)->days();
        $this->artisan('tenants:purge-deleted')->assertSuccessful();
        $this->assertDatabaseMissing('tenants', ['slug' => 'gdpr-salon']);
        $this->assertDatabaseMissing('bookings', ['id' => $this->booking->id]);
        $this->assertDatabaseMissing('users', ['id' => $this->owner->id]);
    }
}
