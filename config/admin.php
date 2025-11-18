<?php

return [
    'default_admin' => [
        'name' => env('ADMIN_NAME', 'Administrateur ProConnect'),
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
        'address' => env('ADMIN_ADDRESS', 'Adresse administrateur'),
        'account_type' => env('ADMIN_ACCOUNT_TYPE', 'pro'),
    ],
];
