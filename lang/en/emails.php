<?php

return [
    'tagline' => 'Online booking',
    'details_title' => 'Booking details',
    'calendar_title' => 'Add to calendar',
    'cancel_label' => 'Cancel booking',
    'cancel_hint' => 'If you need to cancel, click below',
    'location_title' => 'Where to find us',
    'map_button' => 'Show map',
    'guest_title' => 'Guest details',
    'closing_text' => 'We look forward to seeing you. Have a nice day!',
    'all_rights_reserved' => 'All rights reserved.',

    'status' => [
        'confirmed' => 'Confirmed',
        'reminder' => 'Reminder',
        'cancelled' => 'Cancelled',
        'pending' => 'Awaiting confirmation',
    ],

    'labels' => [
        'datetime' => 'Date and time:',
        'service' => 'Service:',
        'worker' => 'Specialist:',
        'price' => 'Price:',
        'place' => 'Location:',
        'name' => 'Name',
        'phone' => 'Phone',
        'email' => 'E-mail',
    ],

    'texts' => [
        'confirmed' => [
            'subject' => 'Booking confirmation – {{service_name}}',
            'greeting' => 'Hello {{customer_name}},',
            'intro' => 'Your booking at {{business_name}} has been confirmed.',
        ],
        'cancelled' => [
            'subject' => 'Booking cancelled – {{service_name}}',
            'greeting' => 'Hello {{customer_name}},',
            'intro' => 'Your booking has been cancelled.',
        ],
        'pending' => [
            'subject' => 'Your booking is awaiting confirmation',
            'greeting' => 'Hello {{customer_name}},',
            'intro' => 'We have received your booking and it is awaiting confirmation.',
        ],
        'reminder' => [
            'subject' => 'Appointment reminder – {{service_name}}',
            'greeting' => 'Hello {{customer_name}},',
            'intro' => 'A quick reminder about your upcoming appointment {{reminder_time_text}}.',
        ],
    ],

    'today' => 'today',
    'tomorrow' => 'tomorrow',
];
