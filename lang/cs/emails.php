<?php

return [
    'tagline' => 'Rezervační systém',
    'details_title' => 'Detaily rezervace',
    'calendar_title' => 'Přidat do kalendáře',
    'cancel_label' => 'Zrušit rezervaci',
    'cancel_hint' => 'Pokud chcete rezervaci zrušit, klikněte níže',
    'location_title' => 'Kde nás najdete?',
    'map_button' => 'Zobrazit mapu',
    'guest_title' => 'Údaje hosta',
    'closing_text' => 'Těšíme se na vaši návštěvu a přejeme hezký den.',
    'all_rights_reserved' => 'Všechna práva vyhrazena.',

    'status' => [
        'confirmed' => 'Potvrzena',
        'reminder' => 'Připomínka',
        'cancelled' => 'Zrušena',
        'pending' => 'Čeká na potvrzení',
    ],

    'labels' => [
        'datetime' => 'Datum a čas:',
        'service' => 'Služba:',
        'worker' => 'Pracovník:',
        'price' => 'Cena:',
        'place' => 'Místo:',
        'name' => 'Jméno',
        'phone' => 'Telefon',
        'email' => 'E-mail',
    ],

    'texts' => [
        'confirmed' => [
            'subject' => 'Potvrzení rezervace – {{service_name}}',
            'greeting' => 'Dobrý den, {{customer_name}}!',
            'intro' => 'Vaše rezervace v {{business_name}} byla potvrzena.',
        ],
        'cancelled' => [
            'subject' => 'Zrušení rezervace – {{service_name}}',
            'greeting' => 'Dobrý den, {{customer_name}}!',
            'intro' => 'Vaše rezervace byla zrušena.',
        ],
        'pending' => [
            'subject' => 'Rezervace čeká na potvrzení',
            'greeting' => 'Dobrý den, {{customer_name}}!',
            'intro' => 'Vaše rezervace byla přijata a čeká na potvrzení.',
        ],
        'reminder' => [
            'subject' => 'Připomínka termínu – {{service_name}}',
            'greeting' => 'Dobrý den, {{customer_name}}!',
            'intro' => 'Připomínáme vám vaši blížící se rezervaci {{reminder_time_text}}.',
        ],
    ],

    'today' => 'dnes',
    'tomorrow' => 'zítra',
];
