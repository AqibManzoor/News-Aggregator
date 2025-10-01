<?php

return [
    'providers' => [
        // Toggle providers on/off
        'newsapi' => [
            'enabled' => env('NEWSAPI_ENABLED', true),
            'class' => App\Services\Providers\NewsApiProvider::class,
        ],
        'guardian' => [
            'enabled' => env('GUARDIAN_ENABLED', true),
            'class' => App\Services\Providers\GuardianProvider::class,
        ],
        'nyt' => [
            'enabled' => env('NYT_ENABLED', true),
            'class' => App\Services\Providers\NytProvider::class,
        ],
    ],

    'defaults' => [
        'language' => env('NEWS_DEFAULT_LANGUAGE', 'en'),
        'pageSize' => env('NEWS_DEFAULT_PAGE_SIZE', 50),
    ],
];
