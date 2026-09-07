<?php

/*
| Default texts of the transactional booking e-mails. A tenant may override the
| texts for its default language in the e-mail template editor; other
| languages always use these defaults (see BookingMailer).
*/

return [
    'tagline' => 'Rezervačný systém',
    'details_title' => 'Detaily rezervácie',
    'calendar_title' => 'Pridať do kalendára',
    'cancel_label' => 'Zrušiť rezerváciu',
    'cancel_hint' => 'Ak chcete zrušiť rezerváciu, kliknite nižšie',
    'location_title' => 'Kde nás nájdete?',
    'map_button' => 'Zobraziť mapu',
    'guest_title' => 'Údaje hosťa',
    'closing_text' => 'Tešíme sa na vašu návštevu a prajeme pekný deň.',
    'all_rights_reserved' => 'Všetky práva vyhradené.',

    'status' => [
        'confirmed' => 'Potvrdená',
        'reminder' => 'Pripomienka',
        'cancelled' => 'Zrušená',
        'pending' => 'Čaká na potvrdenie',
    ],

    'labels' => [
        'datetime' => 'Dátum a čas:',
        'service' => 'Služba:',
        'worker' => 'Pracovník:',
        'price' => 'Cena:',
        'place' => 'Miesto:',
        'name' => 'Meno',
        'phone' => 'Telefón',
        'email' => 'E-mail',
    ],

    'texts' => [
        'confirmed' => [
            'subject' => 'Potvrdenie rezervácie – {{service_name}}',
            'greeting' => 'Dobrý deň, {{customer_name}}!',
            'intro' => 'Vaša rezervácia v {{business_name}} bola potvrdená.',
        ],
        'cancelled' => [
            'subject' => 'Zrušenie rezervácie – {{service_name}}',
            'greeting' => 'Dobrý deň, {{customer_name}}!',
            'intro' => 'Vaša rezervácia bola zrušená.',
        ],
        'pending' => [
            'subject' => 'Rezervácia čaká na potvrdenie',
            'greeting' => 'Dobrý deň, {{customer_name}}!',
            'intro' => 'Vaša rezervácia bola prijatá a čaká na potvrdenie.',
        ],
        'reminder' => [
            'subject' => 'Pripomienka termínu – {{service_name}}',
            'greeting' => 'Dobrý deň, {{customer_name}}!',
            'intro' => 'Pripomíname vám vašu blížiacu sa rezerváciu {{reminder_time_text}}.',
        ],
    ],

    'today' => 'dnes',
    'tomorrow' => 'zajtra',
];
