<?php

declare(strict_types=1);

return [
    'home_button' => 'Return to Home Page',
    'back_button' => 'Go Back',
    'retry_button' => 'Try Again',
    'refresh_button' => 'Refresh Page',

    '401' => [
        'title' => '401 — Unauthorized',
        'header' => 'Authentication Required',
        'message' => 'You must log in to access this page. Please log in or register first.',
        'login_button' => 'Log In',
    ],

    '403' => [
        'title' => '403 — Forbidden',
        'header' => 'Access Forbidden',
        'message' => 'Sorry, you do not have permission to access this page. If you believe this is an error, please contact us.',
    ],

    '404' => [
        'title' => '404 — Page Not Found',
        'header' => 'Page Not Found',
        'message' => 'Sorry, the page you are looking for does not exist. It may have been moved, deleted, or you might have mistyped the URL.',
        'shop_button' => 'Browse Shop',
    ],

    '419' => [
        'title' => '419 — Session Expired',
        'header' => 'Session Expired',
        'message' => 'Your session has expired. Please refresh the page and try again.',
    ],

    '429' => [
        'title' => '429 — Too Many Requests',
        'header' => 'Too Many Requests',
        'message' => "You've sent too many requests in a short time. Please wait a moment and try again.",
    ],

    '500' => [
        'title' => '500 — Server Error',
        'header' => 'Server Error',
        'message' => 'Sorry, something went wrong on our servers. We are working to resolve it as quickly as possible. Please try again later.',
    ],

    '503' => [
        'title' => '503 — Maintenance',
        'header' => "We'll Be Right Back",
        'badge' => 'Maintenance',
        'message' => "Our website is currently down for scheduled maintenance. We're bringing you an even better experience. Please check back soon.",
    ],
];
