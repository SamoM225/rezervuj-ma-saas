<?php

return [
    'paths' => [
        'pricing' => '/cennik',
        'directory' => '/prevadzky',
    ],

    'meta' => [
        'title' => 'rezervuj-ma.online — online rezervačný systém pre salóny a služby',
        'description' => 'Vlastná rezervačná stránka, kalendár pre tím, e-mailové potvrdenia a pripomienky. Zadarmo do 50 rezervácií mesačne, Pro za 5 € mesačne bez limitov. Bez provízií.',
        'site_name' => 'rezervuj-ma.online',
    ],

    'nav' => [
        'how' => 'Ako to funguje',
        'features' => 'Čo dostanete',
        'pricing' => 'Cenník',
        'directory' => 'Prevádzky',
        'faq' => 'Otázky',
        'login' => 'Prihlásenie',
        'signup' => 'Vytvoriť účet zadarmo',
        'language' => 'Jazyk',
    ],

    'hero' => [
        'title' => 'Online rezervácie pre vašu prevádzku.',
        'title_2' => 'Zadarmo, alebo za 5 € bez limitov.',
        'lead' => 'Vlastná rezervačná stránka, kalendár pre celý tím, potvrdenia a pripomienky e-mailom. Nastavíte za desať minút — zákazníci si potom rezervujú sami, aj o polnoci.',
        'cta' => 'Vytvoriť účet zadarmo',
        'demo' => 'Pozrieť ukážku rezervácie',
        'trust' => 'Bez karty. Bez provízií z rezervácií. Zrušíte kedykoľvek.',
        'preview_label' => 'Takto vyzerá rezervačná stránka',
        'preview_step' => 'Vyberte službu',
        'preview_time' => 'Vyberte čas',
        'preview_min' => ':n min',
        'preview_cta' => 'Rezervovať',
    ],

    'how' => [
        'title' => 'Ako to funguje',
        'steps' => [
            ['t' => 'Vytvoríte účet a pridáte služby', 'd' => 'Názov prevádzky, adresa rezervačnej stránky, služby s cenou a trvaním, pracovníci a ich dostupnosť. Žiadna inštalácia.'],
            ['t' => 'Zdieľate odkaz alebo vložíte widget', 'd' => 'rezervuj-ma.online/vasa-prevadzka/booking dáte na Instagram, Google profil alebo vlastný web. Rezervačná stránka nesie vaše farby a logo.'],
            ['t' => 'Zákazníci si rezervujú sami', 'd' => 'Vyberú službu, odborníka a voľný termín. Obaja dostanete e-mail s potvrdením, deň pred termínom príde pripomienka. Vy vidíte všetko v kalendári.'],
        ],
    ],

    'features' => [
        'title' => 'Čo dostanete',
        'lead' => 'Všetko, čo malá prevádzka potrebuje. Nič, za čo by ste mali platiť navyše.',
        'items' => [
            ['t' => 'Rezervačná stránka', 'd' => 'Služby, odborníci, kalendár voľných termínov. Funguje na mobile aj v počítači.'],
            ['t' => 'Kalendár pre tím', 'd' => 'Každý pracovník vidí svoje termíny, blokovanie času a dostupnosť. Presúvanie termínov ťahaním.'],
            ['t' => 'E-maily bez práce', 'd' => 'Potvrdenie, pripomienka, zrušenie a odkaz na zmenu termínu. Šablóny si upravíte v administrácii.'],
            ['t' => 'Ochrana pred nezmyslami', 'd' => 'Overenie e-mailom, ochrana proti botom, blokovanie zákazníkov, ktorí nechodia, pravidlá storna.'],
            ['t' => 'Zákazníci a história', 'd' => 'Kto bol kedy, koľkokrát, aké služby. Export dát kedykoľvek, jedným klikom.'],
            ['t' => 'GDPR bez právnika', 'd' => 'Súhlasy s verziou podmienok, automatické mazanie starých rezervácií, hotové zásady ochrany údajov pre vašu stránku.'],
            ['t' => 'Verejný profil', 'd' => 'Vaša prevádzka v našom adresári a vo vyhľadávačoch — s ponukou, cenami a tlačidlom Rezervovať.'],
            ['t' => 'Slovenčina, čeština, angličtina', 'd' => 'Rezervačná stránka aj e-maily v jazyku vašich zákazníkov.'],
        ],
    ],

    'pricing' => [
        'title' => 'Cenník',
        'lead' => 'Dva plány. Žiadne balíčky, žiadne poplatky za pracovníka, žiadne provízie.',
        'free' => 'Free',
        'pro' => 'Pro',
        'free_price' => '0 €',
        'pro_price' => '5 €',
        'per_month' => 'mesačne',
        'pro_yearly' => 'alebo 50 € ročne (2 mesiace zdarma)',
        'free_note' => 'navždy, bez karty',
        'rows' => [
            ['k' => 'Rezervácie mesačne', 'f' => '50', 'p' => 'bez limitu'],
            ['k' => 'Pracovníci v kalendári', 'f' => 'bez limitu', 'p' => 'bez limitu'],
            ['k' => 'Prevádzky (miesta)', 'f' => '1', 'p' => 'bez limitu'],
            ['k' => 'E-mailové potvrdenia a pripomienky', 'f' => '✓', 'p' => '✓'],
            ['k' => 'Verejný profil a zápis v adresári', 'f' => '✓', 'p' => '✓'],
            ['k' => 'Export dát', 'f' => '✓', 'p' => '✓'],
            ['k' => 'Vlastné logo, farby a úvodný obrázok', 'f' => '—', 'p' => '✓'],
            ['k' => 'Editovateľné e-mailové šablóny', 'f' => '—', 'p' => '✓'],
            ['k' => 'Viacjazyčná rezervačná stránka', 'f' => '—', 'p' => '✓'],
            ['k' => 'Widget na vlastný web', 'f' => '—', 'p' => '✓'],
            ['k' => 'Odkaz „rezervuj-ma" v pätičke', 'f' => 'áno', 'p' => 'nie'],
        ],
        'cta_free' => 'Začať zadarmo',
        'cta_pro' => 'Začať a prejsť na Pro',
        'pro_soon' => 'Pripravujeme',
        'cta_pro_soon' => 'Čoskoro',
        'payment' => 'Pro sa platí cez PayPal (karta alebo PayPal účet). Zrušenie kedykoľvek priamo v administrácii — Pro ostane aktívne do konca zaplateného obdobia.',
        'payment_soon' => 'Teraz je plán Free úplne zadarmo a bez karty. Plán Pro pripravujeme — spustíme ho čoskoro.',
        'compare' => 'Pre porovnanie: bežné systémy v SK/CZ pýtajú 7–19 € mesačne za 200 rezervácií a jedného používateľa, alebo si berú provízie z každej rezervácie.',
    ],

    'categories' => [
        'title' => 'Pre koho to je',
        'lead' => 'Pre každého, kto pracuje na termíny. Vyberte typ prevádzky pri registrácii — adresár a profil sa prispôsobia.',
        'browse' => 'Prezrieť adresár prevádzok',
    ],

    'faq' => [
        'title' => 'Otázky, ktoré sa pýta každý',
        'items' => [
            ['q' => 'Čo sa stane, keď vo Free pláne prekročím 50 rezervácií?', 'a' => 'Online rezervácie sa do konca mesiaca pozastavia a zákazníci uvidia prosbu, aby vás kontaktovali priamo. Vaše dáta ani kalendár sa nemenia. Prechod na Pro ich okamžite obnoví.'],
            ['q' => 'Beriete si provízie z rezervácií?', 'a' => 'Nie. Platíte len 5 € mesačne za Pro, ak ho chcete. Z vašich rezervácií ani zákazníkov si neberieme nič.'],
            ['q' => 'Potrebujem vlastný web?', 'a' => 'Nie. Vaša rezervačná stránka je na adrese rezervuj-ma.online/vasa-prevadzka/booking. Ak web máte, v Pro pláne doň vložíte widget jedným riadkom kódu.'],
            ['q' => 'Ako sa platí Pro?', 'a' => 'Cez PayPal — kartou alebo PayPal účtom, mesačne alebo ročne. Zrušíte kedykoľvek v administrácii, Pro ostane aktívne do konca zaplateného obdobia.'],
            ['q' => 'Kde sú uložené dáta a ako je to s GDPR?', 'a' => 'Na serveroch v Slovenskej republike, za ochranou Cloudflare. Vy ste prevádzkovateľom údajov svojich zákazníkov, my sprostredkovateľom — zmluvu podľa čl. 28 GDPR uzatvárate pri registrácii. Staré rezervácie sa automaticky mažú.'],
            ['q' => 'Môžem odísť a vziať si dáta?', 'a' => 'Áno, kedykoľvek. Rezervácie, zákazníkov, služby aj nastavenia si stiahnete v CSV/JSON. Po zrušení účtu máte 30 dní na stiahnutie, potom všetko zmažeme.'],
        ],
    ],

    'footer' => [
        'tagline' => 'Online rezervačný systém pre salóny, štúdiá a služby.',
        'legal' => 'Právne informácie',
        'product' => 'Produkt',
        'operator' => 'Prevádzkovateľ platformy',
        'not_affiliated' => 'Vaša prevádzka je prevádzkovateľom údajov svojich zákazníkov; platforma je sprostredkovateľom.',
    ],

    'legal' => [
        'title' => 'Právne informácie',
        'lead' => 'Všetky dokumenty na jednom mieste, v jazyku, ktorému rozumiete. Verzie a dátumy účinnosti sú uvedené pri každom dokumente.',
        'version' => 'Verzia :version · účinné od :date',
        'docs' => [
            'terms' => 'Podmienky používania',
            'dpa' => 'Zmluva o spracúvaní osobných údajov (DPA)',
            'privacy' => 'Ochrana osobných údajov (platforma)',
            'cookies' => 'Cookies',
            'aup' => 'Pravidlá prijateľného používania',
            'refunds' => 'Zrušenie predplatného a vrátenie peňazí',
            'imprint' => 'Prevádzkovateľ a kontakty',
            'subprocessors' => 'Zoznam sprostredkovateľov (subprocesorov)',
        ],
    ],

    'directory' => [
        'title' => 'Prevádzky, ktoré prijímajú rezervácie online',
        'lead' => ':count prevádzok podľa typu a mesta. Kliknite na prevádzku a rezervujte si termín — bez registrácie.',
        'empty' => 'Adresár sa práve plní. Prvé prevádzky pribudnú čoskoro.',
        'in_city' => ':category v meste :city',
        'all_cities' => 'Všetky mestá',
        'count' => ':count prevádzok',
        'book' => 'Rezervovať termín',
        'view' => 'Zobraziť profil',
        'meta_category' => ':category — online rezervácia termínu',
        'meta_city' => ':category :city — online rezervácia termínu',
        'meta_desc' => 'Prevádzky v kategórii :category, ktoré prijímajú rezervácie online. Vyberte si termín za pár klikov.',
    ],

    'profile' => [
        'book' => 'Rezervovať termín',
        'services' => 'Ponuka a ceny',
        'team' => 'Tím',
        'contact' => 'Kontakt',
        'hours' => 'Otváracie hodiny',
        'address' => 'Adresa',
        'phone' => 'Telefón',
        'email' => 'E-mail',
        'minutes' => ':n min',
        'more_in' => 'Ďalšie prevádzky: :category',
        'meta_desc' => ':name — :category, :city. Ponuka služieb s cenami a online rezervácia termínu.',
        'powered' => 'Rezervačný systém rezervuj-ma.online',
    ],
];
