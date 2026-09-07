<?php

namespace App\Support;

/**
 * Professional HTML email layout builder for booking emails.
 *
 * Generates a fully-responsive, centered email layout with:
 * - Brand header with configurable accent colour
 * - Reservation details card
 * - Add-to-calendar buttons with real SVG icons
 * - Cancel reservation CTA (optional)
 * - Business address / map section with real pin icon
 * - Guest details section
 * - Professional footer
 *
 * All styles are inlined for maximum email-client compatibility.
 */
class EmailLayoutBuilder
{
    // ──────────────────────────────────────────────
    // Brand colours (can be overridden via business settings)
    // ──────────────────────────────────────────────
    private string $accentColor;

    private string $accentDark;

    private string $bgColor = '#f4f6f8';

    private string $cardBg = '#ffffff';

    private string $textColor = '#333333';

    private string $mutedColor = '#6b7280';

    private string $borderColor = '#e5e7eb';

    private string $businessName;

    private string $businessAddress;

    private string $supportEmail;

    private string $supportPhone;

    private ?string $logoUrl = null;

    /**
     * Template configuration edited by the admin (colours, blocks, texts).
     * Overrides default block visibility, labels, colours, and per-type texts.
     */
    private array $config = [];

    public function __construct(
        string $businessName = '',
        string $businessAddress = '',
        string $supportEmail = '',
        string $supportPhone = '',
        string $accentColor = '#D98AA8',
        ?string $logoUrl = null,
    ) {
        $this->businessName = $businessName;
        $this->businessAddress = $businessAddress;
        $this->supportEmail = $supportEmail;
        $this->supportPhone = $supportPhone;
        $this->accentColor = $accentColor;
        $this->accentDark = $this->darken($accentColor, 15);
        $this->logoUrl = $logoUrl;
    }

    /**
     * Apply the saved configuration array.
     * Merges colours, enables/disables blocks, and overrides texts.
     */
    public function applyConfig(array $config): static
    {
        $this->config = $config;

        // Override colours
        if (! empty($config['colors'])) {
            $c = $config['colors'];
            if (! empty($c['accent'])) {
                $this->accentColor = $c['accent'];
                $this->accentDark = $this->darken($c['accent'], 15);
            }
            if (! empty($c['background'])) {
                $this->bgColor = $c['background'];
            }
            if (! empty($c['card_bg'])) {
                $this->cardBg = $c['card_bg'];
            }
            if (! empty($c['text'])) {
                $this->textColor = $c['text'];
            }
            if (! empty($c['muted'])) {
                $this->mutedColor = $c['muted'];
            }
            if (! empty($c['border'])) {
                $this->borderColor = $c['border'];
            }
        }

        // Optional logo override from the template config
        if (! empty($config['blocks']['header']['logo_url'])) {
            $this->logoUrl = $config['blocks']['header']['logo_url'];
        }

        return $this;
    }

    /**
     * Set the brand logo URL shown in the email header.
     */
    public function setLogoUrl(?string $url): static
    {
        if (! empty($url)) {
            $this->logoUrl = $url;
        }

        return $this;
    }

    /**
     * Return the full default configuration structure.
     * Used on a fresh installation or when resetting to defaults.
     */
    public static function defaultConfig(): array
    {
        return [
            'colors' => [
                'accent' => '#D98AA8',
                'background' => '#f4f6f8',
                'card_bg' => '#ffffff',
                'text' => '#333333',
                'muted' => '#6b7280',
                'border' => '#e5e7eb',
            ],
            'blocks' => [
                'header' => ['enabled' => true, 'show_logo' => true, 'tagline' => __('emails.tagline')],
                'greeting' => ['enabled' => true],
                'details' => ['enabled' => true, 'title' => __('emails.details_title')],
                'calendar' => ['enabled' => true, 'title' => __('emails.calendar_title')],
                'cancel_button' => ['enabled' => true, 'label' => __('emails.cancel_label'), 'hint' => __('emails.cancel_hint')],
                'location' => ['enabled' => true, 'title' => __('emails.location_title'), 'map_button' => __('emails.map_button')],
                'guest' => ['enabled' => true, 'title' => __('emails.guest_title')],
                'closing' => ['enabled' => true, 'text' => __('emails.closing_text')],
                'footer' => ['enabled' => true],
            ],
            'texts' => __('emails.texts'),
        ];
    }

    /**
     * Helper: check if a block is enabled in config (defaults to true).
     */
    private function blockEnabled(string $block): bool
    {
        return $this->config['blocks'][$block]['enabled'] ?? true;
    }

    /**
     * Helper: get a block-level config value with a fallback.
     */
    private function blockOption(string $block, string $key, string $default = ''): string
    {
        return $this->config['blocks'][$block][$key] ?? $default;
    }

    /**
     * Helper: get type-specific text with placeholder support.
     */
    public function configText(string $type, string $field, string $default = ''): string
    {
        return $this->config['texts'][$type][$field] ?? $default;
    }

    // ──────────────────────────────────────────────
    // Public: build full email HTML
    // ──────────────────────────────────────────────

    /**
     * Build a complete booking-confirmation or reminder email.
     *
     * @param  string  $greeting  e.g. "Hello Samuel Majercik,"
     * @param  string  $introText  paragraph under the greeting
     * @param  array  $details  key => value pairs for the reservation card
     * @param  array  $calendarUrls  ['google'=>…, 'apple'=>…, 'outlook'=>…]
     * @param  array  $guestDetails  ['name'=>…, 'phone'=>…, 'email'=>…]
     * @param  string|null  $cancelUrl  optional cancel link
     * @param  string  $type  'confirmed' | 'reminder' | 'cancelled' | 'pending'
     */
    public function build(
        string $greeting,
        string $introText,
        array $details,
        array $calendarUrls = [],
        array $guestDetails = [],
        ?string $cancelUrl = null,
        string $type = 'confirmed',
    ): string {
        $statusIcon = match ($type) {
            'confirmed' => $this->svgCheckCircle(),
            'reminder' => $this->svgBell(),
            'cancelled' => $this->svgXCircle(),
            'pending' => $this->svgClock(),
            default => '',
        };

        $html = $this->docStart();

        if ($this->blockEnabled('header')) {
            $html .= $this->header($statusIcon, $type);
        }

        if ($this->blockEnabled('greeting')) {
            $html .= $this->greetingSection($greeting, $introText);
        }

        if ($this->blockEnabled('details')) {
            $html .= $this->detailsCard($details);
        }

        if ($this->blockEnabled('calendar') && ! empty($calendarUrls) && $type !== 'cancelled') {
            $html .= $this->calendarSection($calendarUrls);
        }

        if ($this->blockEnabled('cancel_button') && $cancelUrl && $type !== 'cancelled') {
            $html .= $this->cancelSection($cancelUrl);
        }

        if ($this->blockEnabled('location') && $this->businessAddress) {
            $html .= $this->locationSection();
        }

        if ($this->blockEnabled('guest') && ! empty($guestDetails)) {
            $html .= $this->guestSection($guestDetails);
        }

        if ($this->blockEnabled('closing')) {
            $html .= $this->closingSection();
        }

        if ($this->blockEnabled('footer')) {
            $html .= $this->footer();
        }

        $html .= $this->docEnd();

        return $html;
    }

    // ──────────────────────────────────────────────
    // Private: sections
    // ──────────────────────────────────────────────

    private function docStart(): string
    {
        return '<!DOCTYPE html><html lang="'.htmlspecialchars(app()->getLocale()).'"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>'.htmlspecialchars($this->businessName).'</title></head>'
            .'<body style="margin:0;padding:0;background:'.$this->bgColor.';font-family:\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">'
            .'<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:'.$this->bgColor.';">'
            .'<tr><td align="center" style="padding:24px 16px;">'
            .'<!--[if mso]><table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0"><tr><td><![endif]-->'
            .'<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;margin:0 auto;">';
    }

    private function docEnd(): string
    {
        return '</table>'
            .'<!--[if mso]></td></tr></table><![endif]-->'
            .'</td></tr></table></body></html>';
    }

    private function header(string $statusIcon, string $type): string
    {
        $label = match ($type) {
            'confirmed' => __('emails.status.confirmed'),
            'reminder' => __('emails.status.reminder'),
            'cancelled' => __('emails.status.cancelled'),
            'pending' => __('emails.status.pending'),
            default => '',
        };

        $showLogo = $this->blockEnabled('header')
            ? ($this->config['blocks']['header']['show_logo'] ?? true)
            : true;
        $tagline = $this->config['blocks']['header']['tagline'] ?? '';

        // Brand block: real logo image when available, otherwise a clean
        // wordmark of the business name (no emoji / placeholder marks).
        if ($showLogo && $this->logoUrl) {
            $brand = '<table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>'
                .'<td style="background:#ffffff;border-radius:14px;padding:10px 18px;">'
                .'<img src="'.htmlspecialchars($this->logoUrl).'" alt="'.htmlspecialchars($this->businessName).'" height="46" style="display:block;max-height:46px;width:auto;border:0;">'
                .'</td></tr></table>';
        } else {
            $brand = '<div style="font-size:25px;font-weight:800;color:#ffffff;letter-spacing:-0.2px;line-height:1.15;">'.htmlspecialchars($this->businessName).'</div>'
                .($tagline ? '<div style="font-size:12px;color:rgba(255,255,255,0.82);margin-top:4px;letter-spacing:0.4px;text-transform:uppercase;">'.htmlspecialchars($tagline).'</div>' : '');
        }

        return '<tr><td>'
            .'<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:'.$this->accentColor.';border-radius:14px 14px 0 0;">'
            .'<tr><td align="center" style="padding:30px 24px 16px;">'
            .$brand
            .'</td></tr>'
            .'<tr><td align="center" style="padding:0 24px 26px;">'
            .'<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="background:rgba(255,255,255,0.16);border-radius:999px;"><tr>'
            .'<td style="padding:7px 8px 7px 14px;" valign="middle">'.$statusIcon.'</td>'
            .'<td style="padding:7px 16px 7px 4px;" valign="middle"><span style="font-size:12px;font-weight:700;color:#ffffff;text-transform:uppercase;letter-spacing:1px;">'.$label.'</span></td>'
            .'</tr></table>'
            .'</td></tr>'
            .'</table>'
            .'</td></tr>';
    }

    private function greetingSection(string $greeting, string $intro): string
    {
        return '<tr><td style="background:'.$this->cardBg.';padding:32px 32px 8px;">'
            .'<div style="font-size:17px;color:'.$this->textColor.';line-height:1.6;">'.htmlspecialchars($greeting).'</div>'
            .'</td></tr>'
            .'<tr><td style="background:'.$this->cardBg.';padding:8px 32px 24px;">'
            .'<div style="font-size:15px;color:'.$this->mutedColor.';line-height:1.6;">'.htmlspecialchars($intro).'</div>'
            .'</td></tr>';
    }

    private function detailsCard(array $details): string
    {
        $rows = '';
        foreach ($details as $label => $value) {
            $rows .= '<tr>'
                .'<td style="padding:10px 0;font-size:14px;color:'.$this->mutedColor.';white-space:nowrap;vertical-align:top;width:140px;">'.htmlspecialchars($label).'</td>'
                .'<td style="padding:10px 0 10px 12px;font-size:14px;font-weight:600;color:'.$this->textColor.';vertical-align:top;">'.htmlspecialchars($value).'</td>'
                .'</tr>';
        }

        return '<tr><td style="background:'.$this->cardBg.';padding:0 32px 28px;">'
            .'<div style="border-radius:10px;border:1px solid '.$this->borderColor.';overflow:hidden;">'
            .'<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:'.$this->cardBg.';">'
            .'<tr><td colspan="2" style="padding:16px 20px 10px;border-bottom:2px solid '.$this->accentColor.';">'
            .'<span style="font-size:16px;font-weight:700;color:'.$this->accentColor.';">'.htmlspecialchars($this->blockOption('details', 'title', __('emails.details_title'))).'</span>'
            .'</td></tr>'
            .'<tr><td colspan="2" style="padding:4px 20px 0;">'
            .'<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">'
            .$rows
            .'</table></td></tr>'
            .'<tr><td colspan="2" style="padding:8px;"></td></tr>'
            .'</table></div>'
            .'</td></tr>';
    }

    private function calendarSection(array $urls): string
    {
        $google = $urls['google'] ?? '#';
        $apple = $urls['apple'] ?? '#';
        $outlook = $urls['outlook'] ?? '#';

        return '<tr><td style="background:'.$this->cardBg.';padding:0 32px 24px;">'
            .'<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f9fafb;border-radius:10px;border:1px solid '.$this->borderColor.';">'
            .'<tr><td align="center" style="padding:20px 16px 12px;">'
            .'<span style="font-size:14px;font-weight:600;color:'.$this->textColor.';">'.htmlspecialchars($this->blockOption('calendar', 'title', __('emails.calendar_title'))).'</span>'
            .'</td></tr>'
            .'<tr><td align="center" style="padding:0 16px 20px;">'
            .'<table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>'

            // Google
            .'<td style="padding:0 6px;">'
            .'<a href="'.htmlspecialchars($google).'" target="_blank" style="text-decoration:none;">'
            .'<table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>'
            .'<td align="center" style="background:#ffffff;border:1px solid '.$this->borderColor.';border-radius:10px;padding:12px 18px;">'
            .$this->svgGoogleCalendar()
            .'<div style="font-size:11px;font-weight:600;color:'.$this->textColor.';margin-top:6px;">Google</div>'
            .'</td></tr></table></a></td>'

            // Apple / iCal
            .'<td style="padding:0 6px;">'
            .'<a href="'.htmlspecialchars($apple).'" target="_blank" style="text-decoration:none;">'
            .'<table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>'
            .'<td align="center" style="background:#ffffff;border:1px solid '.$this->borderColor.';border-radius:10px;padding:12px 18px;">'
            .$this->svgAppleCalendar()
            .'<div style="font-size:11px;font-weight:600;color:'.$this->textColor.';margin-top:6px;">iCal</div>'
            .'</td></tr></table></a></td>'

            // Outlook
            .'<td style="padding:0 6px;">'
            .'<a href="'.htmlspecialchars($outlook).'" target="_blank" style="text-decoration:none;">'
            .'<table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>'
            .'<td align="center" style="background:#ffffff;border:1px solid '.$this->borderColor.';border-radius:10px;padding:12px 18px;">'
            .$this->svgOutlookCalendar()
            .'<div style="font-size:11px;font-weight:600;color:'.$this->textColor.';margin-top:6px;">Outlook</div>'
            .'</td></tr></table></a></td>'

            .'</tr></table></td></tr></table>'
            .'</td></tr>';
    }

    private function cancelSection(string $cancelUrl): string
    {
        return '<tr><td style="background:'.$this->cardBg.';padding:0 32px 28px;" align="center">'
            .'<div style="font-size:13px;color:'.$this->mutedColor.';margin-bottom:12px;">'.htmlspecialchars($this->blockOption('cancel_button', 'hint', __('emails.cancel_hint'))).'</div>'
            .'<a href="'.htmlspecialchars($cancelUrl).'" target="_blank" '
            .'style="display:inline-block;padding:14px 36px;background:'.$this->accentColor.';color:#ffffff;font-size:14px;font-weight:700;text-decoration:none;border-radius:8px;letter-spacing:0.3px;">'
            .htmlspecialchars($this->blockOption('cancel_button', 'label', __('emails.cancel_label'))).'</a>'
            .'</td></tr>';
    }

    private function locationSection(): string
    {
        $mapsUrl = 'https://www.google.com/maps/search/?api=1&query='.urlencode($this->businessAddress);

        return '<tr><td style="background:'.$this->cardBg.';padding:0 32px 28px;">'
            .'<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f9fafb;border-radius:10px;border:1px solid '.$this->borderColor.';">'
            .'<tr><td align="center" style="padding:20px 20px 8px;">'
            .'<span style="font-size:16px;font-weight:700;color:'.$this->accentColor.';">'.htmlspecialchars($this->blockOption('location', 'title', __('emails.location_title'))).'</span>'
            .'</td></tr>'
            .'<tr><td style="padding:8px 20px 4px;">'
            .'<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"><tr>'
            .'<td width="28" valign="top" style="padding-right:10px;">'.$this->svgMapPin($this->accentColor).'</td>'
            .'<td valign="top" style="font-size:14px;color:'.$this->textColor.';line-height:1.5;">'.htmlspecialchars($this->businessAddress).'</td>'
            .'</tr></table></td></tr>'
            .($this->supportPhone ? '<tr><td style="padding:4px 20px;">'
            .'<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"><tr>'
            .'<td width="28" valign="top" style="padding-right:10px;">'.$this->svgPhone($this->accentColor).'</td>'
            .'<td valign="top" style="font-size:14px;color:'.$this->textColor.';line-height:1.5;">'.htmlspecialchars($this->supportPhone).'</td>'
            .'</tr></table></td></tr>' : '')
            .($this->supportEmail ? '<tr><td style="padding:4px 20px;">'
            .'<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"><tr>'
            .'<td width="28" valign="top" style="padding-right:10px;">'.$this->svgMail($this->accentColor).'</td>'
            .'<td valign="top"><a href="mailto:'.htmlspecialchars($this->supportEmail).'" style="font-size:14px;color:'.$this->accentColor.';text-decoration:none;">'.htmlspecialchars($this->supportEmail).'</a></td>'
            .'</tr></table></td></tr>' : '')
            .'<tr><td align="center" style="padding:16px 20px 20px;">'
            .'<a href="'.htmlspecialchars($mapsUrl).'" target="_blank" '
            .'style="display:inline-block;padding:10px 24px;background:'.$this->accentColor.';color:#ffffff;font-size:13px;font-weight:600;text-decoration:none;border-radius:8px;">'
            .$this->svgMapPinSmall().' '.htmlspecialchars($this->blockOption('location', 'map_button', __('emails.map_button'))).'</a>'
            .'</td></tr>'
            .'</table>'
            .'</td></tr>';
    }

    private function guestSection(array $guest): string
    {
        $name = $guest['name'] ?? '';
        $phone = $guest['phone'] ?? '';
        $email = $guest['email'] ?? '';

        $rows = '';
        if ($name) {
            $rows .= '<tr><td style="padding:4px 20px;">'
                .'<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"><tr>'
                .'<td width="28" valign="middle" style="padding-right:10px;">'.$this->svgUser($this->accentColor).'</td>'
                .'<td valign="middle" style="font-size:14px;color:'.$this->textColor.';">'.htmlspecialchars($name).'</td>'
                .'</tr></table></td></tr>';
        }
        if ($phone) {
            $rows .= '<tr><td style="padding:4px 20px;">'
                .'<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"><tr>'
                .'<td width="28" valign="middle" style="padding-right:10px;">'.$this->svgPhone($this->accentColor).'</td>'
                .'<td valign="middle" style="font-size:14px;color:'.$this->textColor.';">'.htmlspecialchars($phone).'</td>'
                .'</tr></table></td></tr>';
        }
        if ($email) {
            $rows .= '<tr><td style="padding:4px 20px;">'
                .'<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"><tr>'
                .'<td width="28" valign="middle" style="padding-right:10px;">'.$this->svgMail($this->accentColor).'</td>'
                .'<td valign="middle"><a href="mailto:'.htmlspecialchars($email).'" style="font-size:14px;color:'.$this->accentColor.';text-decoration:none;">'.htmlspecialchars($email).'</a></td>'
                .'</tr></table></td></tr>';
        }

        return '<tr><td style="background:'.$this->cardBg.';padding:0 32px 28px;">'
            .'<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f9fafb;border-radius:10px;border:1px solid '.$this->borderColor.';">'
            .'<tr><td align="center" style="padding:20px 20px 10px;">'
            .'<span style="font-size:16px;font-weight:700;color:'.$this->accentColor.';">'.htmlspecialchars($this->blockOption('guest', 'title', __('emails.guest_title'))).'</span>'
            .'</td></tr>'
            .$rows
            .'<tr><td style="padding:12px;"></td></tr>'
            .'</table>'
            .'</td></tr>';
    }

    private function closingSection(): string
    {
        return '<tr><td style="background:'.$this->cardBg.';padding:8px 32px 28px;border-radius:0 0 12px 12px;">'
            .'<div style="font-size:14px;color:'.$this->mutedColor.';line-height:1.6;text-align:center;">'.htmlspecialchars($this->blockOption('closing', 'text', __('emails.closing_text'))).'</div>'
            .'</td></tr>';
    }

    private function footer(): string
    {
        return '<tr><td align="center" style="padding:20px 16px 8px;">'
            .'<div style="font-size:12px;color:#9ca3af;line-height:1.5;">'
            .htmlspecialchars($this->businessName)
            .($this->businessAddress ? ' &bull; '.htmlspecialchars($this->businessAddress) : '')
            .'</div>'
            .'<div style="font-size:11px;color:#d1d5db;margin-top:8px;">'
            .'&copy; '.date('Y').' '.htmlspecialchars($this->businessName).'. '.__('emails.all_rights_reserved')
            .'</div>'
            .'</td></tr>';
    }

    // ──────────────────────────────────────────────
    // SVG Icons (inline, email-safe)
    // ──────────────────────────────────────────────

    private function svgCheckCircle(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';
    }

    private function svgBell(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>';
    }

    private function svgXCircle(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
    }

    private function svgClock(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>';
    }

    private function svgGoogleCalendar(): string
    {
        // Simplified Google Calendar logo
        return '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 48 48">'
            .'<path fill="#4285f4" d="M36 4H12C7.6 4 4 7.6 4 12v24c0 4.4 3.6 8 8 8h24c4.4 0 8-3.6 8-8V12c0-4.4-3.6-8-8-8z" opacity=".15"/>'
            .'<path fill="#4285f4" d="M34 20h-8v-8h-4v8h-8v4h8v8h4v-8h8z"/>'
            .'<rect x="10" y="10" width="28" height="28" rx="4" fill="none" stroke="#4285f4" stroke-width="2"/>'
            .'<line x1="16" y1="6" x2="16" y2="14" stroke="#4285f4" stroke-width="2" stroke-linecap="round"/>'
            .'<line x1="32" y1="6" x2="32" y2="14" stroke="#4285f4" stroke-width="2" stroke-linecap="round"/>'
            .'</svg>';
    }

    private function svgAppleCalendar(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 48 48">'
            .'<rect x="6" y="10" width="36" height="32" rx="4" fill="#ffffff" stroke="#333333" stroke-width="2"/>'
            .'<rect x="6" y="10" width="36" height="10" rx="4" fill="#333333"/>'
            .'<line x1="16" y1="6" x2="16" y2="14" stroke="#333333" stroke-width="2.5" stroke-linecap="round"/>'
            .'<line x1="32" y1="6" x2="32" y2="14" stroke="#333333" stroke-width="2.5" stroke-linecap="round"/>'
            .'<text x="24" y="36" text-anchor="middle" fill="#333333" font-size="14" font-weight="bold" font-family="Arial">'.date('j').'</text>'
            .'</svg>';
    }

    private function svgOutlookCalendar(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 48 48">'
            .'<rect x="6" y="10" width="36" height="32" rx="4" fill="#ffffff" stroke="#0078d4" stroke-width="2"/>'
            .'<rect x="6" y="10" width="36" height="10" rx="4" fill="#0078d4"/>'
            .'<line x1="16" y1="6" x2="16" y2="14" stroke="#0078d4" stroke-width="2.5" stroke-linecap="round"/>'
            .'<line x1="32" y1="6" x2="32" y2="14" stroke="#0078d4" stroke-width="2.5" stroke-linecap="round"/>'
            .'<text x="24" y="36" text-anchor="middle" fill="#0078d4" font-size="14" font-weight="bold" font-family="Arial">'.date('j').'</text>'
            .'</svg>';
    }

    private function svgMapPin(string $color = '#D98AA8'): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="'.$color.'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>';
    }

    private function svgMapPinSmall(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>';
    }

    private function svgPhone(string $color = '#D98AA8'): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="'.$color.'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';
    }

    private function svgMail(string $color = '#D98AA8'): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="'.$color.'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>';
    }

    private function svgUser(string $color = '#D98AA8'): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="'.$color.'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    private function darken(string $hex, int $percent): string
    {
        $hex = ltrim($hex, '#');
        $r = max(0, hexdec(substr($hex, 0, 2)) - (int) (255 * $percent / 100));
        $g = max(0, hexdec(substr($hex, 2, 2)) - (int) (255 * $percent / 100));
        $b = max(0, hexdec(substr($hex, 4, 2)) - (int) (255 * $percent / 100));

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}
