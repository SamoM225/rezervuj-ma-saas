<?php

return [
    'paths' => [
        'pricing' => '/cenik',
        'directory' => '/provozovny',
    ],

    'meta' => [
        'title' => 'rezervuj-ma.online — online rezervační systém pro salony a služby',
        'description' => 'Vlastní rezervační stránka, kalendář pro tým, e-mailová potvrzení a připomínky. Zdarma do 50 rezervací měsíčně, Pro za 5 € měsíčně bez limitů. Bez provizí.',
        'site_name' => 'rezervuj-ma.online',
    ],

    'nav' => [
        'how' => 'Jak to funguje',
        'features' => 'Co dostanete',
        'pricing' => 'Ceník',
        'directory' => 'Provozovny',
        'faq' => 'Otázky',
        'login' => 'Přihlášení',
        'signup' => 'Vytvořit účet zdarma',
        'language' => 'Jazyk',
    ],

    'hero' => [
        'title' => 'Online rezervace pro vaši provozovnu.',
        'title_2' => 'Zdarma, nebo za 5 € bez limitů.',
        'lead' => 'Vlastní rezervační stránka, kalendář pro celý tým, potvrzení a připomínky e-mailem. Nastavíte za deset minut — zákazníci si potom rezervují sami, i o půlnoci.',
        'cta' => 'Vytvořit účet zdarma',
        'demo' => 'Podívat se na ukázku rezervace',
        'trust' => 'Bez karty. Bez provizí z rezervací. Zrušíte kdykoli.',
        'preview_label' => 'Takto vypadá rezervační stránka',
        'preview_step' => 'Vyberte službu',
        'preview_time' => 'Vyberte čas',
        'preview_min' => ':n min',
        'preview_cta' => 'Rezervovat',
    ],

    'how' => [
        'title' => 'Jak to funguje',
        'steps' => [
            ['t' => 'Vytvoříte účet a přidáte služby', 'd' => 'Název provozovny, adresa rezervační stránky, služby s cenou a délkou, pracovníci a jejich dostupnost. Žádná instalace.'],
            ['t' => 'Sdílíte odkaz nebo vložíte widget', 'd' => 'rezervuj-ma.online/vase-provozovna/booking dáte na Instagram, Google profil nebo vlastní web. Rezervační stránka nese vaše barvy a logo.'],
            ['t' => 'Zákazníci si rezervují sami', 'd' => 'Vyberou službu, odborníka a volný termín. Oba dostanete e-mail s potvrzením, den před termínem přijde připomínka. Vy vidíte všechno v kalendáři.'],
        ],
    ],

    'features' => [
        'title' => 'Co dostanete',
        'lead' => 'Všechno, co malá provozovna potřebuje. Nic, za co byste měli platit navíc.',
        'items' => [
            ['t' => 'Rezervační stránka', 'd' => 'Služby, odborníci, kalendář volných termínů. Funguje na mobilu i v počítači.'],
            ['t' => 'Kalendář pro tým', 'd' => 'Každý pracovník vidí své termíny, blokování času a dostupnost. Přesouvání termínů tažením.'],
            ['t' => 'E-maily bez práce', 'd' => 'Potvrzení, připomínka, zrušení a odkaz na změnu termínu. Šablony si upravíte v administraci.'],
            ['t' => 'Ochrana před nesmysly', 'd' => 'Ověření e-mailem, ochrana proti botům, blokování zákazníků, kteří nechodí, pravidla storna.'],
            ['t' => 'Zákazníci a historie', 'd' => 'Kdo byl kdy, kolikrát, jaké služby. Export dat kdykoli, jedním klikem.'],
            ['t' => 'GDPR bez právníka', 'd' => 'Souhlasy s verzí podmínek, automatické mazání starých rezervací, hotové zásady ochrany údajů pro vaši stránku.'],
            ['t' => 'Veřejný profil', 'd' => 'Vaše provozovna v našem adresáři a ve vyhledávačích — s nabídkou, cenami a tlačítkem Rezervovat.'],
            ['t' => 'Čeština, slovenština, angličtina', 'd' => 'Rezervační stránka i e-maily v jazyce vašich zákazníků.'],
        ],
    ],

    'pricing' => [
        'title' => 'Ceník',
        'lead' => 'Dva plány. Žádné balíčky, žádné poplatky za pracovníka, žádné provize.',
        'free' => 'Free',
        'pro' => 'Pro',
        'free_price' => '0 €',
        'pro_price' => '5 €',
        'per_month' => 'měsíčně',
        'pro_yearly' => 'nebo 50 € ročně (2 měsíce zdarma)',
        'free_note' => 'navždy, bez karty',
        'rows' => [
            ['k' => 'Rezervace měsíčně', 'f' => '50', 'p' => 'bez limitu'],
            ['k' => 'Pracovníci v kalendáři', 'f' => 'bez limitu', 'p' => 'bez limitu'],
            ['k' => 'Provozovny (místa)', 'f' => '1', 'p' => 'bez limitu'],
            ['k' => 'E-mailová potvrzení a připomínky', 'f' => '✓', 'p' => '✓'],
            ['k' => 'Veřejný profil a zápis v adresáři', 'f' => '✓', 'p' => '✓'],
            ['k' => 'Export dat', 'f' => '✓', 'p' => '✓'],
            ['k' => 'Vlastní logo, barvy a úvodní obrázek', 'f' => '—', 'p' => '✓'],
            ['k' => 'Editovatelné e-mailové šablony', 'f' => '—', 'p' => '✓'],
            ['k' => 'Vícejazyčná rezervační stránka', 'f' => '—', 'p' => '✓'],
            ['k' => 'Widget na vlastní web', 'f' => '—', 'p' => '✓'],
            ['k' => 'Odkaz „rezervuj-ma" v patičce', 'f' => 'ano', 'p' => 'ne'],
        ],
        'cta_free' => 'Začít zdarma',
        'cta_pro' => 'Začít a přejít na Pro',
        'pro_soon' => 'Připravujeme',
        'cta_pro_soon' => 'Brzy',
        'payment' => 'Pro se platí přes PayPal (karta nebo PayPal účet). Zrušení kdykoli přímo v administraci — Pro zůstane aktivní do konce zaplaceného období.',
        'payment_soon' => 'Nyní je plán Free zcela zdarma a bez karty. Plán Pro připravujeme — spustíme jej brzy.',
        'compare' => 'Pro srovnání: běžné systémy v CZ/SK chtějí 7–19 € měsíčně za 200 rezervací a jednoho uživatele, nebo si berou provize z každé rezervace.',
    ],

    'categories' => [
        'title' => 'Pro koho to je',
        'lead' => 'Pro každého, kdo pracuje na termíny. Vyberte typ provozovny při registraci — adresář a profil se přizpůsobí.',
        'browse' => 'Prohlédnout adresář provozoven',
    ],

    'faq' => [
        'title' => 'Otázky, které se ptá každý',
        'items' => [
            ['q' => 'Co se stane, když ve Free plánu překročím 50 rezervací?', 'a' => 'Online rezervace se do konce měsíce pozastaví a zákazníci uvidí prosbu, aby vás kontaktovali přímo. Vaše data ani kalendář se nemění. Přechod na Pro je okamžitě obnoví.'],
            ['q' => 'Berete si provize z rezervací?', 'a' => 'Ne. Platíte jen 5 € měsíčně za Pro, pokud ho chcete. Z vašich rezervací ani zákazníků si nebereme nic.'],
            ['q' => 'Potřebuji vlastní web?', 'a' => 'Ne. Vaše rezervační stránka je na adrese rezervuj-ma.online/vase-provozovna/booking. Pokud web máte, v Pro plánu do něj vložíte widget jedním řádkem kódu.'],
            ['q' => 'Jak se platí Pro?', 'a' => 'Přes PayPal — kartou nebo PayPal účtem, měsíčně nebo ročně. Zrušíte kdykoli v administraci, Pro zůstane aktivní do konce zaplaceného období.'],
            ['q' => 'Kde jsou uložena data a jak je to s GDPR?', 'a' => 'Na serverech ve Slovenské republice, za ochranou Cloudflare. Vy jste správcem údajů svých zákazníků, my zpracovatelem — smlouvu podle čl. 28 GDPR uzavíráte při registraci. Staré rezervace se automaticky mažou.'],
            ['q' => 'Můžu odejít a vzít si data?', 'a' => 'Ano, kdykoli. Rezervace, zákazníky, služby i nastavení si stáhnete v CSV/JSON. Po zrušení účtu máte 30 dní na stažení, potom všechno smažeme.'],
        ],
    ],

    'footer' => [
        'tagline' => 'Online rezervační systém pro salony, studia a služby.',
        'legal' => 'Právní informace',
        'product' => 'Produkt',
        'operator' => 'Provozovatel platformy',
        'not_affiliated' => 'Vaše provozovna je správcem údajů svých zákazníků; platforma je zpracovatelem.',
    ],

    'legal' => [
        'title' => 'Právní informace',
        'lead' => 'Všechny dokumenty na jednom místě, v jazyce, kterému rozumíte. Verze a data účinnosti jsou uvedeny u každého dokumentu.',
        'version' => 'Verze :version · účinné od :date',
        'docs' => [
            'terms' => 'Podmínky používání',
            'dpa' => 'Smlouva o zpracování osobních údajů (DPA)',
            'privacy' => 'Ochrana osobních údajů (platforma)',
            'cookies' => 'Cookies',
            'aup' => 'Pravidla přijatelného používání',
            'refunds' => 'Zrušení předplatného a vrácení peněz',
            'imprint' => 'Provozovatel a kontakty',
            'subprocessors' => 'Seznam zpracovatelů (subprocesorů)',
        ],
    ],

    'directory' => [
        'title' => 'Provozovny, které přijímají rezervace online',
        'lead' => ':count provozoven podle typu a města. Klikněte na provozovnu a rezervujte si termín — bez registrace.',
        'empty' => 'Adresář se právě plní. První provozovny přibudou brzy.',
        'in_city' => ':category ve městě :city',
        'all_cities' => 'Všechna města',
        'count' => ':count provozoven',
        'book' => 'Rezervovat termín',
        'view' => 'Zobrazit profil',
        'meta_category' => ':category — online rezervace termínu',
        'meta_city' => ':category :city — online rezervace termínu',
        'meta_desc' => 'Provozovny v kategorii :category, které přijímají rezervace online. Vyberte si termín za pár kliků.',
    ],

    'profile' => [
        'book' => 'Rezervovat termín',
        'services' => 'Nabídka a ceny',
        'team' => 'Tým',
        'contact' => 'Kontakt',
        'hours' => 'Otevírací doba',
        'address' => 'Adresa',
        'phone' => 'Telefon',
        'email' => 'E-mail',
        'minutes' => ':n min',
        'more_in' => 'Další provozovny: :category',
        'meta_desc' => ':name — :category, :city. Nabídka služeb s cenami a online rezervace termínu.',
        'powered' => 'Rezervační systém rezervuj-ma.online',
    ],
];
