<?php

use App\Jobs\SendBookingReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Booking Reminder Scheduler
|--------------------------------------------------------------------------
| Runs every hour and sends email reminders to customers whose
| appointments are within the configured reminder window (default 24 h).
*/
Schedule::job(new SendBookingReminders)->hourly();

/*
| GDPR data retention — deletes bookings older than config('gdpr.retention_days')
| (default 2 years) once a day, so personal data is not kept indefinitely.
*/
Schedule::command('bookings:purge-expired')->dailyAt('03:30');

// Deleted accounts are purged 30 days after the owner asked (DPA clause 12).
Schedule::command('tenants:purge-deleted')->dailyAt('04:00');
