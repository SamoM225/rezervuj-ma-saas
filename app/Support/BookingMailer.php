<?php

namespace App\Support;

use App\Mail\BookingNotification;
use App\Models\Booking;
use App\Models\BusinessSetting;
use App\Services\IcsCalendarService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

/**
 * The single place every customer e-mail about a booking is produced.
 *
 * Confirmation, pending, cancellation and reminder messages all go through the
 * same EmailLayoutBuilder and the template configuration the admin edits under
 * "E-mailové šablóny", so they look identical and every customisation applies.
 */
class BookingMailer
{
    public const CONFIRMED = 'confirmed';

    public const PENDING = 'pending';

    public const CANCELLED = 'cancelled';

    public const REMINDER = 'reminder';

    /**
     * Send one notification. Never throws: a mail failure must not break the
     * booking flow, so problems are logged instead.
     *
     * @param  array<string, string>  $extra  additional placeholders, e.g. reminder texts
     */
    public static function send(Booking $booking, string $type, array $extra = []): bool
    {
        if (! BusinessSetting::get('send_email_notifications', true) || ! $booking->customer_email) {
            return false;
        }

        try {
            [$subject, $html] = self::render($booking, $type, $extra);
            Mail::to($booking->customer_email, $booking->customer_name)
                ->send(new BookingNotification($booking->id, $type, $subject, $html));

            return true;
        } catch (\Throwable $e) {
            Log::error('BookingMailer: e-mail could not be sent', [
                'booking_id' => $booking->id,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Build subject + HTML for a booking e-mail.
     *
     * @return array{0: string, 1: string}
     */
    public static function render(Booking $booking, string $type, array $extra = []): array
    {
        // Customers get the e-mail in the language they booked in, whatever the
        // request (or console command) locale is at send time.
        $previous = app()->getLocale();
        app()->setLocale($booking->locale ?: $previous);

        try {
            return self::renderInCurrentLocale($booking, $type, $extra);
        } finally {
            app()->setLocale($previous);
        }
    }

    /** @return array{0: string, 1: string} */
    private static function renderInCurrentLocale(Booking $booking, string $type, array $extra): array
    {
        $booking->loadMissing(['service', 'worker', 'location']);

        $businessName = (string) BusinessSetting::get('business_name', config('app.name'));
        $businessAddress = (string) BusinessSetting::get('business_address', '');
        $supportEmail = (string) BusinessSetting::get('support_email', config('mail.from.address', ''));
        $supportPhone = (string) BusinessSetting::get('support_phone', '');
        $logoPath = BusinessSetting::get('business_logo_path');

        $builder = new EmailLayoutBuilder(
            businessName: $businessName,
            businessAddress: $businessAddress,
            supportEmail: $supportEmail,
            supportPhone: $supportPhone,
            accentColor: (string) BusinessSetting::get('email_accent_color', '#D98AA8'),
            logoUrl: $logoPath ? url(Storage::url($logoPath)) : null,
        );

        $config = BusinessSetting::get('email_template_config');
        $config = is_string($config) ? json_decode($config, true) : $config;
        if (is_array($config)) {
            // Tenant-written texts are in the tenant's own language; for any other
            // language fall back to the translated defaults and keep only layout choices.
            if (app()->getLocale() !== (string) BusinessSetting::get('default_language', Tenancy::current()?->locale ?? 'sk')) {
                unset($config['texts']);
                foreach ($config['blocks'] ?? [] as $block => $options) {
                    $config['blocks'][$block] = array_intersect_key((array) $options, ['enabled' => 1, 'show_logo' => 1]);
                }
            }
            $builder->applyConfig($config);
        }

        $placeholders = self::placeholders($booking, $businessName, $businessAddress, $supportEmail, $supportPhone) + $extra;
        $defaults = EmailLayoutBuilder::defaultConfig()['texts'][$type] ?? EmailLayoutBuilder::defaultConfig()['texts'][self::CONFIRMED];

        $text = fn (string $field) => strtr($builder->configText($type, $field, $defaults[$field] ?? ''), $placeholders);

        $details = array_filter([
            __('emails.labels.datetime') => $placeholders['{{booking_date}}'].', '.$placeholders['{{booking_time_range}}'],
            __('emails.labels.service') => $placeholders['{{service_name}}'],
            __('emails.labels.worker') => $placeholders['{{worker_name}}'],
            __('emails.labels.price') => $placeholders['{{booking_price}}'],
            __('emails.labels.place') => $booking->location?->address ?: ($booking->location?->name ?: ''),
        ], fn ($value) => trim((string) $value) !== '');

        $isActive = $type !== self::CANCELLED;

        $html = $builder->build(
            greeting: $text('greeting'),
            introText: $text('intro'),
            details: $details,
            calendarUrls: $isActive ? IcsCalendarService::buildCalendarUrls($booking) : [],
            guestDetails: [
                'name' => $booking->customer_name,
                'phone' => $booking->customer_phone,
                'email' => $booking->customer_email,
            ],
            cancelUrl: $isActive
                ? URL::temporarySignedRoute('booking.cancel.show', now()->addDays(60), ['booking' => $booking->id])
                : null,
            type: $type,
        );

        return [$text('subject'), $html];
    }

    /** @return array<string, string> */
    private static function placeholders(Booking $booking, string $businessName, string $businessAddress, string $supportEmail, string $supportPhone): array
    {
        $start = Carbon::parse($booking->start_time)->format('H:i');
        $end = Carbon::parse($booking->end_time)->format('H:i');
        $price = $booking->service?->price;

        return [
            '{{customer_name}}' => (string) $booking->customer_name,
            '{{service_name}}' => (string) ($booking->service?->name ?? ''),
            '{{worker_name}}' => (string) ($booking->worker?->name ?? ''),
            '{{booking_date}}' => Carbon::parse($booking->date)->locale(app()->getLocale())->isoFormat(app()->getLocale() === 'en' ? 'D MMM YYYY' : 'D. M. YYYY'),
            '{{booking_start_time}}' => $start,
            '{{booking_end_time}}' => $end,
            '{{booking_time_range}}' => $start.' – '.$end,
            '{{booking_price}}' => is_numeric($price) ? Money::format((float) $price) : (string) ($price ?? ''),
            '{{reminder_time_text}}' => self::relativeDay(Carbon::parse($booking->date.' '.$booking->start_time)),
            '{{booking_link}}' => URL::temporarySignedRoute('booking.confirmation', now()->addDays(60), ['booking' => $booking->id]),
            '{{business_name}}' => $businessName,
            '{{business_address}}' => $businessAddress,
            '{{support_email}}' => $supportEmail,
            '{{support_phone}}' => $supportPhone,
        ];
    }

    /** "today" / "tomorrow" in the current locale, otherwise the localized date. */
    public static function relativeDay(Carbon $startsAt): string
    {
        if ($startsAt->isToday()) {
            return __('emails.today');
        }
        if ($startsAt->isTomorrow()) {
            return __('emails.tomorrow');
        }

        return $startsAt->locale(app()->getLocale())->isoFormat(app()->getLocale() === 'en' ? 'D MMM YYYY' : 'D. M. YYYY');
    }
}
