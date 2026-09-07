<?php

return [
    /*
    | Route pattern for a tenant slug in the URL: rezervuj-ma.online/{slug}/booking
    */
    'slug_pattern' => '[a-z0-9](?:[a-z0-9-]{1,48}[a-z0-9])?',

    /*
    | First URL segments that can never be a tenant slug (platform routes,
    | locales, legal pages, assets). Checked at signup and by the resolver.
    */
    'reserved_slugs' => [
        'admin', 'worker', 'login', 'logout', 'register', 'registracia', 'registrace', 'signup',
        'account', 'api', 'web', 'internal', 'legal', 'pravne', 'privacy', 'terms', 'cookies',
        'sk', 'cs', 'en', 'de', 'pl', 'hu', 'up', 'build', 'storage', 'images', 'assets',
        'sitemap.xml', 'robots.txt', 'favicon.ico', 'widget.js', 'language', 'booking',
        'platform', 'billing', 'paypal', 'webhooks', 'demo-api', 'blog', 'help', 'support',
        'rezervuj-ma', 'rezervuj', 'www', 'mail', 'app', 'status', 'search', 'salony', 'salons',
    ],

    /*
    | Plans. `null` = unlimited. Booking limits count public + staff-created,
    | non-cancelled bookings created in the current calendar month.
    */
    'plans' => [
        'free' => [
            'bookings_per_month' => 50,
            'locations' => 1,
            'custom_branding' => false,
            'multilingual_booking_page' => false,
            'embed_widget' => false,
        ],
        'pro' => [
            'bookings_per_month' => null,
            'locations' => null,
            'custom_branding' => true,
            'multilingual_booking_page' => true,
            'embed_widget' => true,
        ],
    ],

    /*
    | Locales the platform UI ships with. Tenants pick a default and a subset for
    | their booking page.
    */
    'locales' => ['sk', 'cs', 'en'],
    'default_locale' => 'sk',

    /*
    | Business categories a tenant can choose (drives directory grouping and the
    | schema.org LocalBusiness subtype on the public profile).
    */
    'categories' => [
        'hair' => 'HairSalon',
        'barber' => 'HairSalon',
        'beauty' => 'BeautySalon',
        'nails' => 'NailSalon',
        'massage' => 'HealthAndBeautyBusiness',
        'spa' => 'DaySpa',
        'physio' => 'Physiotherapy',
        'tattoo' => 'TattooParlor',
        'dental' => 'Dentist',
        'fitness' => 'HealthClub',
        'pets' => 'LocalBusiness',
        'auto' => 'AutoRepair',
        'education' => 'EducationalOrganization',
        'other' => 'LocalBusiness',
    ],

    /*
    | Supervisory authorities (GDPR Art. 77) and consumer ADR bodies named in
    | the generated tenant privacy notice and booking terms, by tenant country.
    | Countries not listed fall back to a generic wording.
    */
    'authorities' => [
        'SK' => 'Úrad na ochranu osobných údajov Slovenskej republiky, Hraničná 12, 820 07 Bratislava (dataprotection.gov.sk)',
        'CZ' => 'Úřad pro ochranu osobních údajů, Pplk. Sochora 27, 170 00 Praha 7 (uoou.gov.cz)',
        'PL' => 'Urząd Ochrony Danych Osobowych, ul. Stawki 2, 00-193 Warszawa (uodo.gov.pl)',
        'HU' => 'Nemzeti Adatvédelmi és Információszabadság Hatóság, 1055 Budapest, Falk Miksa u. 9-11 (naih.hu)',
        'AT' => 'Österreichische Datenschutzbehörde, Barichgasse 40-42, 1030 Wien (dsb.gv.at)',
        'DE' => 'Datenschutzaufsichtsbehörde des Bundeslandes, in dem der Verantwortliche niedergelassen ist (bfdi.bund.de)',
        'IE' => 'Data Protection Commission, 21 Fitzwilliam Square South, Dublin 2 (dataprotection.ie)',
        'GB' => "Information Commissioner's Office, Wycliffe House, Water Lane, Wilmslow SK9 5AF (ico.org.uk)",
    ],
    'adr_bodies' => [
        'SK' => 'Slovenská obchodná inšpekcia (soi.sk) alebo iný subjekt alternatívneho riešenia sporov zapísaný v zozname Ministerstva hospodárstva SR',
        'CZ' => 'Česká obchodní inspekce (coi.cz, adr.coi.cz)',
        'PL' => 'Inspekcja Handlowa (uokik.gov.pl)',
        'HU' => 'a lakóhelye szerint illetékes békéltető testület (bekeltetes.hu)',
        'AT' => 'Verbraucherschlichtung Austria (verbraucherschlichtung.at)',
        'DE' => 'Allgemeine Verbraucherschlichtungsstelle des Zentrums für Schlichtung e.V. (verbraucher-schlichter.de)',
        'IE' => 'Competition and Consumer Protection Commission (ccpc.ie)',
        'GB' => 'a certified alternative dispute resolution provider (gov.uk/consumer-protection-rights)',
    ],
];
