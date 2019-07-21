<?php
return [
    'admin-email' => env('ADMIN_EMAIL', 'admin@example.com'),
    'currency_code' => env('CURRENCY_CODE', 'USD'),
    'currency_smallest_unit' => (float) env('CURRENCY_SMALLEST_UNIT', '0.01'),
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

    'extended_invoice' => env('EXTENDED_INVOICE', null),

    'ignore_recaptcha' => !is_null(env('IGNORE_RECAPTCHA', null)),
    'recaptcha_secret_key' => env('RECAPTCHA_SECRET_KEY', ''),
    'recaptcha_site_key' => env('RECAPTCHA_SITE_KEY', ''),
    'recaptcha_url' => env('RECAPTCHA_URL', 'https://www.google.com/recaptcha/api/siteverify'),
    'stripe_publishable_key' =>env('STRIPE_PUBLISHABLE_KEY'),
    'stripe_secret_key' => env('STRIPE_SECRET_KEY'),
];