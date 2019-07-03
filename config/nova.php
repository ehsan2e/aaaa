<?php

return [
    'admin-email' => env('ADMIN_EMAIL', 'admin@example.com'),
    'language' => [
        'en' => [
            'code' => 'en',
            'display' => 'En',
            'name' => 'English',
        ],
        'fr' => [
            'code' => 'fr',
            'display' => 'Fr',
            'name' => 'Français',
        ],
    ],
    'box_category_code' => env('BOX_CATEGORY_CODE', 'box'),
    'box_service_category_code' => env('BOX_SERVICE_CATEGORY_CODE', 'box_service'),

    'recaptcha_secret_key' => env('RECAPTCHA_SECRET_KEY', ''),
    'recaptcha_site_key' => env('RECAPTCHA_SITE_KEY', ''),
    'recaptcha_url' => env('RECAPTCHA_URL', 'https://www.google.com/recaptcha/api/siteverify'),
];