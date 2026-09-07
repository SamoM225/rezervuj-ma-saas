<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Models\BusinessSetting;
use App\Models\Tenant;
use App\Support\BookingMailer;
use App\Support\Tenancy;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Sends a reminder e-mail to customers whose appointment starts within the
 * configured window (default 24 hours). Scheduled hourly in routes/console.php
 * and also reachable through the token-protected /internal/scheduler/run URL.
 */
class SendBookingReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * How many hours before the booking to send the reminder.
     */
    protected int $hoursBefore;

    public function __construct(int $hoursBefore = 24)
    {
        $this->hoursBefore = $hoursBefore;
    }

    public function handle(): void
    {
        Tenant::query()->active()->each(function (Tenant $tenant) {
            Tenancy::runAs($tenant, fn () => $this->handleTenant());
        });
    }

    protected function handleTenant(): void
    {
        if (! BusinessSetting::get('send_email_notifications', true)) {
            return;
        }

        $reminderHours = (int) BusinessSetting::get('reminder_hours_before', $this->hoursBefore);

        // Find bookings that are happening in the next `$reminderHours` hours
        // and haven't been reminded yet
        $windowStart = now();
        $windowEnd = now()->addHours($reminderHours);

        // Date and time are separate columns; filter on the date range in SQL and
        // on the exact start moment in PHP so this works on every database driver.
        $bookings = Booking::with(['service', 'worker', 'location'])
            ->whereIn('status', ['confirmed', 'pending'])
            ->whereNotNull('customer_email')
            ->where('reminder_sent', false)
            ->whereBetween('date', [$windowStart->toDateString(), $windowEnd->toDateString()])
            ->get()
            ->filter(fn (Booking $booking) => Carbon::parse($booking->date.' '.$booking->start_time)->between($windowStart, $windowEnd));

        Log::info("SendBookingReminders: Found {$bookings->count()} bookings to remind");

        foreach ($bookings as $booking) {
            try {
                $this->sendReminder($booking, $reminderHours);

                $booking->update(['reminder_sent' => true]);

                Log::info('SendBookingReminders: Reminder sent', ['booking_id' => $booking->id]);
            } catch (\Exception $e) {
                Log::error('SendBookingReminders: Failed to send reminder', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    protected function sendReminder(Booking $booking, int $hoursBefore): void
    {
        // {{reminder_time_text}} is filled in by the mailer in the booking's language.
        $sent = BookingMailer::send($booking, BookingMailer::REMINDER, [
            '{{hours_before}}' => (string) $hoursBefore,
        ]);

        if (! $sent) {
            throw new \RuntimeException('Reminder e-mail was not sent.');
        }
    }
}
