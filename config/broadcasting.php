<?php

return [
    'default' => env('BROADCAST_DRIVER', 'pusher'),
    'middleware' => [
        'web',
        'auth:sanctum',
    ],

    'connections' => [
        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER', 'ap2'),
                'host' => env('PUSHER_HOST') ?: 'api-'.env('PUSHER_APP_CLUSTER', 'ap2').'.pusher.com',
                'port' => env('PUSHER_PORT', 443),
                'scheme' => env('PUSHER_SCHEME', 'https'),
                'encrypted' => true,
                'useTLS' => env('PUSHER_SCHEME', 'https') === 'https',
                'curl_options' => [
                    CURLOPT_SSL_VERIFYHOST => 2,
                    CURLOPT_SSL_VERIFYPEER => 1,
                    CURLOPT_TIMEOUT => 30,
                ],
                'encryption_master_key_base64' => env('PUSHER_ENCRYPTION_MASTER_KEY'),
                'auth' => [
                    'headers' => [
                        'Authorization' => 'Bearer {token}',
                        'X-Socket-ID' => '{socket_id}',
                        'X-CSRF-TOKEN' => '{csrf_token}',
                        'Accept' => 'application/json',
                    ],
                    'endpoint' => '/broadcasting/auth',
                    'host' => config('app.url'),
                ],
                'authEndpoint' => '/broadcasting/auth',
            ],
            'client_options' => [
                'cluster' => env('PUSHER_APP_CLUSTER', 'mt1'),
                'useTLS' => env('PUSHER_SCHEME', 'https') === 'https',
                'wsHost' => env('PUSHER_HOST', 'ws-'.(env('PUSHER_APP_CLUSTER', 'mt1')).'.pusher.com'),
                'wsPort' => env('PUSHER_PORT', 80),
                'wssPort' => env('PUSHER_PORT', 443),
                'disableStats' => true,
                'forceTLS' => env('PUSHER_SCHEME', 'https') === 'https',
                'enabledTransports' => ['ws', 'wss'],
            ],
            'pong_timeout' => 30,
            'stats' => false,
        ],

        'ably' => [
            'driver' => 'ably',
            'key' => env('ABLY_KEY'),
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => env('REDIS_QUEUE', 'default'),
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],
    ],
];






