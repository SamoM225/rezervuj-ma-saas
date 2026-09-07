<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BusinessSetting;
use Carbon\Carbon;
use Illuminate\Http\Response;

/**
 * Service for generating RFC-5545 compliant iCalendar (.ics) files.
 *
 * Usage:
 *   $ics = IcsCalendarService::fromBooking($booking);
 *   return IcsCalendarService::download($ics, 'rezervacia.ics');
 *
 * Or get just the raw content:
 *   $content = IcsCalendarService::fromBooking($booking);
 */
class IcsCalendarService
{
    /**
     * Generate a complete .ics string from a Booking model.
     */
    public static function fromBooking(Booking $booking): string
    {
        $booking->loadMissing(['service', 'worker', 'location']);

        $businessName    = BusinessSetting::get('business_name', 'Rezervácia');
        // Prefer the booking's specific location address, fall back to the business address.
        $businessAddress = $booking->location?->address
            ?: ($booking->location?->name ?: BusinessSetting::get('business_address', ''));
        $supportEmail    = BusinessSetting::get('support_email', '');
        $supportPhone    = BusinessSetting::get('support_phone', '');

        $serviceName = $booking->service->name ?? 'Rezervácia';

        $startDateTime = Carbon::parse($booking->date . ' ' . $booking->start_time);
        $endDateTime   = Carbon::parse($booking->date . ' ' . $booking->end_time);

        // Build description lines
        $descriptionLines = [
            "Služba: {$serviceName}",
            "Meno: {$booking->customer_name}",
        ];

        if ($booking->worker) {
            $descriptionLines[] = "Pracovník: {$booking->worker->name}";
        }

        if ($businessAddress) {
            $descriptionLines[] = "Adresa: {$businessAddress}";
        }

        if ($supportEmail) {
            $descriptionLines[] = "Kontakt: {$supportEmail}";
        }

        if ($supportPhone) {
            $descriptionLines[] = "Telefón: {$supportPhone}";
        }

        if ($booking->notes) {
            $descriptionLines[] = "Poznámka: {$booking->notes}";
        }

        return self::generate(
            summary:     "{$serviceName} - {$businessName}",
            startTime:   $startDateTime,
            endTime:     $endDateTime,
            location:    $businessAddress,
            description: implode('\n', $descriptionLines),
            uid:         $booking->id . '@' . request()->getHost(),
            organizer:   $supportEmail ?: null,
        );
    }

    /**
     * Generate a raw .ics string from individual parameters.
     *
     * All datetime values are treated as local (without timezone conversion)
     * to keep the simplicity of the booking system.
     */
    public static function generate(
        string  $summary,
        Carbon  $startTime,
        Carbon  $endTime,
        string  $location    = '',
        string  $description = '',
        ?string $uid         = null,
        ?string $organizer   = null,
        string  $status      = 'CONFIRMED',
    ): string {
        $uid     = $uid ?? uniqid('booking-', true) . '@' . request()->getHost();
        $dtstamp = Carbon::now('UTC')->format('Ymd\THis\Z');
        $dtstart = $startTime->format('Ymd\THis');
        $dtend   = $endTime->format('Ymd\THis');

        // RFC-5545: fold long lines, escape special characters
        $summary     = self::escapeIcsText($summary);
        $description = self::escapeIcsText($description);
        $location    = self::escapeIcsText($location);

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//BookingSystem//SK//v1.0',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-CALNAME:Rezervácia',
            'BEGIN:VEVENT',
            "UID:{$uid}",
            "DTSTAMP:{$dtstamp}",
            "DTSTART:{$dtstart}",
            "DTEND:{$dtend}",
            "SUMMARY:{$summary}",
            "DESCRIPTION:{$description}",
        ];

        if ($location !== '') {
            $lines[] = "LOCATION:{$location}";
        }

        if ($organizer) {
            $lines[] = "ORGANIZER;CN=Prevádzka:mailto:{$organizer}";
        }

        // 30-minute reminder alarm
        $lines = array_merge($lines, [
            "STATUS:{$status}",
            'BEGIN:VALARM',
            'TRIGGER:-PT30M',
            'ACTION:DISPLAY',
            'DESCRIPTION:Pripomienka: ' . $summary,
            'END:VALARM',
            'END:VEVENT',
            'END:VCALENDAR',
        ]);

        // RFC-5545 requires CRLF line endings
        return implode("\r\n", $lines) . "\r\n";
    }

    /**
     * Return a download Response for the given ICS content.
     */
    public static function download(string $icsContent, string $filename = 'rezervacia.ics'): Response
    {
        return response($icsContent)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Build calendar "Add to calendar" URLs for Google, Outlook, and Apple.
     */
    public static function buildCalendarUrls(Booking $booking): array
    {
        $booking->loadMissing(['service', 'location']);

        $businessName    = BusinessSetting::get('business_name', 'Rezervácia');
        $businessAddress = $booking->location?->address
            ?: ($booking->location?->name ?: BusinessSetting::get('business_address', ''));

        $title    = ($booking->service->name ?? 'Rezervácia') . ' - ' . $businessName;
        $startDt  = Carbon::parse($booking->date . ' ' . $booking->start_time);
        $endDt    = Carbon::parse($booking->date . ' ' . $booking->end_time);
        $desc     = "Rezervácia: " . ($booking->service->name ?? 'Služba') . "\nMeno: " . $booking->customer_name;
        $location = $businessAddress;

        // Google Calendar
        $googleUrl = 'https://calendar.google.com/calendar/render?' . http_build_query([
            'action'   => 'TEMPLATE',
            'text'     => $title,
            'dates'    => $startDt->format('Ymd\THis') . '/' . $endDt->format('Ymd\THis'),
            'details'  => $desc,
            'location' => $location,
        ]);

        // .ics download URL for Apple
        $icsUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'booking.ics', now()->addDays(30), ['booking' => $booking->id]
        );

        // Outlook
        $outlookUrl = 'https://outlook.live.com/calendar/0/deeplink/compose?' . http_build_query([
            'subject' => $title,
            'startdt' => $startDt->toIso8601String(),
            'enddt'   => $endDt->toIso8601String(),
            'body'    => $desc,
            'location' => $location,
        ]);

        return [
            'google'  => $googleUrl,
            'apple'   => $icsUrl,
            'outlook' => $outlookUrl,
            'ics'     => $icsUrl,
        ];
    }

    /**
     * Escape text values according to RFC-5545.
     */
    private static function escapeIcsText(string $text): string
    {
        // Replace actual newlines with \n literal
        $text = str_replace(["\r\n", "\r", "\n"], '\n', $text);
        // Escape commas and semicolons
        $text = str_replace(',', '\,', $text);
        $text = str_replace(';', '\;', $text);

        return $text;
    }
}
