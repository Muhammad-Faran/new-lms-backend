<?php

return [
    'base_url' => env('CREDIT_ENGINE_BASE_URL', 'http://157.245.80.37:5000/api/shippers/'),
    'ofac_nacta_url' => env('OFAC_NACTA_URL', 'http://157.245.80.37:2525/match'),
    'endpoints' => [
        'info' => '',
        'kyc' => '/kyc',
        'pricing' => '/pricing',
        'credit_score' => '/credit-score-rating',
    ],
    'headers' => [
        'API-Key' => env('CREDIT_ENGINE_API_KEY', 'default-api-key'),
        'API-Secret' => env('CREDIT_ENGINE_API_SECRET', 'default-api-secret'),
    ],
];
