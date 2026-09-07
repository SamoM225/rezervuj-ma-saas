<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <title>{{ __("auth.code_mail.subject_{$purpose}", ['code' => $code], $locale) }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f2ee;font-family:-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#1e1c19;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f2ee;padding:32px 16px;">
        <tr><td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:#ffffff;border-radius:14px;padding:32px;">
                <tr><td style="font-size:14px;color:#6b6760;padding-bottom:8px;">{{ $businessName }}</td></tr>
                <tr><td style="font-size:22px;font-weight:700;padding-bottom:12px;">{{ __("auth.code_mail.heading_{$purpose}", [], $locale) }}</td></tr>
                <tr><td style="font-size:15px;line-height:1.55;color:#3d3a35;padding-bottom:20px;">{{ __("auth.code_mail.intro_{$purpose}", [], $locale) }}</td></tr>
                <tr><td align="center" style="padding:8px 0 20px;">
                    <div style="display:inline-block;font-size:34px;letter-spacing:.35em;font-weight:700;padding:14px 22px;border-radius:12px;background:#f4f2ee;font-family:SFMono-Regular,Consolas,monospace;">{{ $code }}</div>
                </td></tr>
                <tr><td style="font-size:13px;line-height:1.5;color:#6b6760;">{{ __('auth.code_mail.expires', ['minutes' => $ttl], $locale) }}<br>{{ __('auth.code_mail.ignore', [], $locale) }}</td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
