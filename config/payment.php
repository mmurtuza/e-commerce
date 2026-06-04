<?php

declare(strict_types=1);

return [
    'default' => env('PAYMENT_DEFAULT', 'cod'),

    'gateways' => [
        'cod' => [
            'enabled' => env('COD_ENABLED', true),
            'label' => 'Cash on Delivery',
        ],

        'sslcommerz' => [
            'enabled' => env('SSLCOMMERZ_ENABLED', false),
            'store_id' => env('SSLCOMMERZ_STORE_ID'),
            'store_password' => env('SSLCOMMERZ_STORE_PASSWORD'),
            'sandbox' => env('SSLCOMMERZ_SANDBOX', true),
            'success_url' => '/payment/sslcommerz/success',
            'fail_url' => '/payment/sslcommerz/fail',
            'cancel_url' => '/payment/sslcommerz/cancel',
            'ipn_url' => '/payment/sslcommerz/ipn',
        ],

        'bkash' => [
            'enabled' => env('BKASH_ENABLED', false),
            'app_key' => env('BKASH_APP_KEY'),
            'app_secret' => env('BKASH_APP_SECRET'),
            'username' => env('BKASH_USERNAME'),
            'password' => env('BKASH_PASSWORD'),
            'sandbox' => env('BKASH_SANDBOX', true),
            'callback_url' => env('BKASH_CALLBACK_URL'),
        ],

    ],
];
