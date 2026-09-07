{{-- Colour tokens from the admin appearance settings, shared by the public pages and the login screen. --}}
@php
    $theme = \App\Models\Setting::getThemeColors();
    $accent = $theme['main_accent'];
        $hex = ltrim($accent, '#');
        if (strlen($hex) === 3) { $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2]; }
        if (strlen($hex) !== 6) { $hex = 'c19a3e'; }
        [$r, $g, $b] = [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
        $mix = fn (int $c, float $p, int $t) => (int) round($c + ($t - $c) * $p);
        $toHex = fn (int $r, int $g, int $b) => sprintf('#%02x%02x%02x', $r, $g, $b);
        $lin = fn (int $v) => ($v /= 255) <= 0.03928 ? $v / 12.92 : pow(($v + 0.055) / 1.055, 2.4);
        $luminance = 0.2126 * $lin($r) + 0.7152 * $lin($g) + 0.0722 * $lin($b);
@endphp
    <style>
        :root {
            --main-accent: {{ $accent }};
            --text-color: {{ $theme['text_color'] }};
            --booking-bg: {{ $theme['booking_bg_color'] }};
            --accent-rgb: {{ $r }}, {{ $g }}, {{ $b }};
            --accent-soft: {{ $toHex($mix($r, 0.88, 255), $mix($g, 0.88, 255), $mix($b, 0.88, 255)) }};
            --accent-muted: {{ $toHex($mix($r, 0.55, 255), $mix($g, 0.55, 255), $mix($b, 0.55, 255)) }};
            --accent-strong: {{ $toHex($mix($r, 0.22, 0), $mix($g, 0.22, 0), $mix($b, 0.22, 0)) }};
            --accent-contrast: {{ $luminance > 0.62 ? '#1f1a14' : '#ffffff' }};
            /* shared components (dialogs, buttons) follow the configured accent on public pages */
            --gold: var(--main-accent);
            --gold-deep: var(--accent-strong);
            --gold-ink: var(--accent-strong);
            --gold-soft: var(--accent-soft);
            --gold-tint: var(--accent-soft);
            --gold-rgb: var(--accent-rgb);
        }
        body { background: var(--booking-bg); color: var(--text-color); }
    </style>
