# Bezpečnosť a nasadenie

## Pred nasadením do produkcie

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://vasa-domena.sk

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

MAIL_MAILER=smtp            # + prihlasovacie údaje k SMTP
SCHEDULER_TOKEN=            # dlhý náhodný reťazec, ak plánovač spúšťa externý cron
```

```bash
php artisan key:generate
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan migrate --force
npm run build
```

Po prvom prihlásení zmeňte heslá štartovacích účtov zo seedera a odstráňte účty,
ktoré nepatria tímu. Správcom zapnite dvojfaktorové overenie (ikona štítu v ľavom
dolnom rohu administrácie).

## Čo aplikácia robí sama

- **Prihlásenie:** obmedzenie pokusov podľa e-mailu aj IP, voliteľné TOTP 2FA so
  záložnými kódmi, regenerácia session po prihlásení.
- **Roly:** `superadmin`, `admin`, `worker`. Pracovník vidí a mení len vlastný kalendár;
  administrátor nemôže vytvoriť ani upraviť správcovský účet.
- **Verejné formuláre:** honeypot, šifrovaný časový token a behaviorálny signál
  (`config/antibot.php`), rate limiting, blokovanie problémových zákazníkov.
- **Odkazy v e-mailoch** (potvrdenie, ICS, zrušenie) sú podpísané a časovo obmedzené,
  takže osobné údaje nie sú dostupné podľa ID.
- **Hlavičky:** CSP, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`,
  v produkcii HSTS (`app/Http/Middleware/SecurityHeaders.php`).
- **GDPR:** pri rezervácii sa ukladá čas súhlasu a verzia zásad; rezervácie staršie ako
  `GDPR_RETENTION_DAYS` (predvolene 730) sa automaticky mažú.

## Hlásenie chýb

Bezpečnostné problémy hláste prevádzkovateľovi na kontaktný e-mail uvedený v nastaveniach,
nie verejne.
