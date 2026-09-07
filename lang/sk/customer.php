<?php

/*
| Customer-facing texts outside the booking widget itself: confirmation and
| cancellation pages, controller messages, the bug-report dialog and the
| tenant legal pages. The widget strings live in widget.php.
*/

return [
    'meta' => [
        'default_title' => ':name – online rezervácia',
        'default_description' => 'Rezervujte si termín online v :name. Vyberte si službu, odborníka a voľný termín za pár klikov.',
        'og_locale' => 'sk_SK',
    ],

    'confirmation' => [
        'title' => 'Rezervácia prijatá',
        'tagline' => 'Vaša rezervácia',
        'cancelled_title' => 'Rezervácia je zrušená',
        'pending_title' => 'Rezerváciu sme prijali',
        'confirmed_title' => 'Termín je rezervovaný',
        'cancelled_lead' => 'Tento termín už neplatí. Nový si môžete vybrať kedykoľvek.',
        'pending_lead' => 'Čaká na potvrdenie. Dáme vám vedieť e-mailom, hneď ako ho potvrdíme.',
        'confirmed_lead' => 'Potvrdenie sme poslali na :email. Pred termínom vám pošleme aj pripomienku.',
        'service' => 'Služba',
        'worker' => 'Odborník',
        'date' => 'Dátum',
        'time' => 'Čas',
        'place' => 'Miesto',
        'price' => 'Cena',
        'add_to_calendar' => 'Pridať do kalendára',
        'apple_ics' => 'Apple / .ics',
        'pick_new' => 'Vybrať nový termín',
        'back' => 'Späť na rezervácie',
        'cancel_booking' => 'Zrušiť rezerváciu',
    ],

    'cancellation' => [
        'title' => 'Zrušenie rezervácie',
        'cancelled_title' => 'Rezervácia je zrušená',
        'cancelled_lead' => 'Termín sme uvoľnili. Ak si to rozmyslíte, nový si vyberiete za pár klikov.',
        'ask_title' => 'Zrušiť túto rezerváciu?',
        'ask_lead' => 'Termín sa uvoľní pre ostatných a pošleme vám potvrdenie o zrušení.',
        'appointment' => 'Termín',
        'too_late' => 'Online zrušenie je možné najneskôr :hours hodín pred termínom.',
        'call_us' => 'Zavolajte nám prosím na',
        'back' => 'Späť',
        'confirm' => 'Áno, zrušiť rezerváciu',
        'keep' => 'Ponechať termín',
    ],

    'flash' => [
        'created' => 'Rezervácia bola vytvorená.',
        'already_cancelled' => 'Rezervácia už bola zrušená.',
        'cancelled' => 'Rezervácia bola zrušená.',
        'too_late_online' => 'Tento termín už nie je možné zrušiť online.',
        'status_updated' => 'Stav rezervácie bol aktualizovaný.',
        'moved' => 'Rezervácia bola presunutá.',
        'deleted' => 'Rezervácia bola odstránená.',
        'slot_held' => 'Termín je dočasne rezervovaný.',
    ],

    'errors' => [
        'pick_time' => 'Vyberte čas rezervácie.',
        'online_unavailable' => 'Online rezervácie nie sú momentálne dostupné.',
        'quota_public' => 'Online rezervácie sú tento mesiac vyčerpané. Kontaktujte prevádzku priamo.',
        'quota_admin' => 'Mesačný limit rezervácií plánu Free (:limit) je vyčerpaný. Prejdite na Pro pre neobmedzené rezervácie.',
        'contact_required' => 'Meno, e-mail a telefón sú povinné.',
        'location_mismatch' => 'Vybrané miesto nepatrí pracovníkovi.',
        'min_advance' => 'Termín je možné rezervovať až po minimálnom predstihu.',
        'out_of_range' => 'Termín je mimo povoleného obdobia rezervácie.',
        'closed_day' => 'Vybraný dátum je zatvorený.',
        'daily_limit' => 'Dosiahli ste maximálny počet rezervácií na deň.',
        'not_a_worker' => 'Vybraný používateľ nie je pracovník.',
        'service_not_offered' => 'Pracovník neposkytuje vybranú službu.',
        'fixed_slots' => 'Vyberte jeden z ponúknutých pevných časov rezervácie.',
        'slot_unavailable' => 'Vybraný termín už nie je dostupný.',
        'slot_gone' => 'Termín už nie je dostupný.',
        'slot_held_by_other' => 'Termín si práve vyberá niekto iný. Zvoľte prosím iný čas.',
        'cannot_move_cancelled' => 'Zrušenú rezerváciu nie je možné presunúť.',
        'worker_only_own' => 'Pracovník môže meniť len vlastné rezervácie.',
        'worker_only_self' => 'Pracovník môže vytvoriť rezerváciu iba pre seba.',
    ],

    'bug' => [
        'title' => 'Nahlásiť problém',
        'sub' => 'Napíšte nám, čo sa stalo. Ozveme sa čo najskôr.',
        'close' => 'Zavrieť',
        'name' => 'Vaše meno',
        'email' => 'E-mail',
        'summary' => 'Čo sa pokazilo (krátko)',
        'impact' => 'Závažnosť',
        'impact_unknown' => 'Neviem posúdiť',
        'impact_blocking' => 'Nedá sa dokončiť rezervácia',
        'impact_data' => 'Nesprávne údaje',
        'impact_design' => 'Zobrazenie',
        'description' => 'Popis',
        'description_ph' => 'Čo ste robili, čo ste čakali a čo sa stalo namiesto toho.',
        'steps' => 'Kroky na zopakovanie (nepovinné)',
        'attachments' => 'Snímky obrazovky',
        'attachments_hint' => 'Obrázky alebo PDF, najviac 4 MB na súbor.',
        'cancel' => 'Zrušiť',
        'send' => 'Odoslať hlásenie',
    ],

    'legal' => [
        'privacy_title' => 'Ochrana osobných údajov',
        'terms_title' => 'Podmienky rezervácie',
        'book' => 'Rezervovať termín',
        'privacy_link' => 'Ochrana osobných údajov',
        'terms_link' => 'Podmienky rezervácie',
        'authority_default' => 'dozorný orgán pre ochranu osobných údajov v krajine sídla prevádzkovateľa',
        'adr_default' => 'orgán ochrany spotrebiteľa alebo subjekt alternatívneho riešenia sporov v krajine sídla poskytovateľa',
    ],
];
