<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Define the routes where CORS applies

    'allowed_methods' => ['*'], // Allow all HTTP methods

    'allowed_origins' => ['*' // Backend URL
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Allow all headers

    'exposed_headers' => [],

    'max_age' => 3600, // Cache duration for preflight requests

    'supports_credentials' => true, // Set to true if using cookies or authentication
];

