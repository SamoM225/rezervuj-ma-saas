# rezervuj-ma.online

Multi-tenant rezervačný systém pre salóny, štúdiá a ambulancie (Laravel 12, PHP 8.4). Každá prevádzka má vlastnú
rezervačnú stránku `rezervuj-ma.online/<slug>/booking`, verejný profil `/<slug>` a administráciu `/<slug>/admin`.
Marketingový web, právny hub, adresár prevádzok (kategória × mesto) a sitemap bežia na apexe v sk/cs/en.

Plán a rozhodnutia: `docs/PLAN-multitenant-saas.md`.

## Plány

| | Free | Pro (5 €/mes., 50 €/rok) |
|---|---|---|
| Rezervácie mesačne | 50 | bez limitu |
| Prevádzky | 1 | bez limitu |
| Jazyky rezervačnej stránky | 1 | sk/cs/en |
| Vlastné farby, logo, fotografia, e-mailové šablóny, widget na vlastný web | – | ✓ |
| Odkaz „rezervuj-ma“ v pätičke | áno | nie |

Platby: PayPal Subscriptions (`app/Services/PayPal`, webhook `POST /webhooks/paypal`). Plány sa vytvoria príkazom
`php artisan paypal:setup-plans` po doplnení `PAYPAL_*` do `.env`.

## Lokálny vývoj

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed --seeder=DemoTenantSeeder   # tenant "demo", admin@example.test / ChangeMeBeforeProduction!
npm run build            # alebo npm run dev
php artisan serve --port=8010
php artisan test --compact
vendor/bin/pint --dirty
```

Užitočné adresy: `/` (web), `/demo` (profil), `/demo/booking` (rezervácia), `/demo/admin` (administrácia),
`/legal/terms`, `/prevadzky`, `/sitemap.xml`.

## Nasadenie na box (192.168.100.50)

Kód beží v kontajneri `web-rezervuj` (serversideup/php:8.4-fpm-nginx, quadlet `deploy/rezervuj.container`),
nginx na porte **8081**, PHP-FPM na 127.0.0.1:9001 (port 9000 patrí inej aplikácii na serveri). MariaDB a Redis sú zdieľané
kontajnery na hostiteľskej sieti. Plánovač spúšťa systemd timer `rezervuj-schedule.timer` každú minútu.

```powershell
# prvé nasadenie: DB, .env, systemd jednotky, composer, migrácie, demo tenant
powershell -ExecutionPolicy Bypass -File deploy.ps1 -Bootstrap -PublicUrl http://192.168.100.50:8081
# ďalšie nasadenia (po zmene frontendu pridaj -Build)
powershell -ExecutionPolicy Bypass -File deploy.ps1
```

Po nastavení Cloudflare Tunnel hostname (`rezervuj-ma.online` → `localhost:8081`):

```bash
bash /opt/webstack/set-public-url-rezervuj.sh https://rezervuj-ma.online
```

V `/opt/webstack/rezervuj/.env` treba ručne doplniť `MAIL_*` (kým je `MAIL_MAILER=log`, overovacie kódy sa len
logujú), `PAYPAL_*` a `OPERATOR_*`; potom `podman exec -u 33 web-rezervuj php artisan config:cache`.

## Štruktúra

- `app/Support/Tenancy.php`, `app/Models/Concerns/BelongsToTenant.php`, `app/Http/Middleware/ResolveTenant.php` – tenancy
- `app/Support/PlanLimits.php` – limity plánov (`config/tenancy.php`)
- `app/Http/Controllers/Platform/*` – web, registrácia, adresár, sitemap, PayPal webhook
- `app/Http/Controllers/TenantLegalController.php` – generované podmienky a informácie o spracúvaní údajov prevádzky
- `resources/views/legal/*` – právne dokumenty platformy (sk/cs/en)
- `lang/{sk,cs,en}` – `site` (web), `widget`/`customer` (zákazník), `emails`, `ui`/`admin`/`calendar` (administrácia), `auth`
- `deploy/` – quadlet, timer, bootstrap a deploy skripty
