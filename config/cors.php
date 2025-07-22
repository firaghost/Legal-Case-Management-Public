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


    'paths' => [
        'api/*',
        'broadcasting/auth',
        'broadcasting/socket.io/*',
        'sanctum/csrf-cookie',
        'login',
        'logout',
        'register',
        'password/*',
        'email/verify/*',
        'forgot-password',
        'reset-password',
        'user/profile-information',
        'user/password',
        'email/verification-notification',
        'user/confirmed-password-status',
        'user/confirm-password',
        'user/confirmed-password',
        'two-factor-challenge',
        'user/two-factor-authentication',
        'user/confirmed-two-factor-authentication',
        'user/two-factor-qr-code',
        'user/two-factor-secret-key',
        'user/two-factor-recovery-codes',
        'user/other-browser-sessions',
        'user/profile-photo',
        'current-user',
        'api/broadcasting/auth',
        'broadcasting/*',
        'socket.io/*',
        'chat/*',
        'pusher-test',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Request Methods
    |--------------------------------------------------------------------------
    |
    | This array contains the list of HTTP methods that are allowed when
    | making cross-origin requests.
    |
    */
    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Request Headers
    |--------------------------------------------------------------------------
    |
    | This array contains the list of HTTP headers that are allowed when
    | making cross-origin requests.
    |
    */
    'allowed_headers' => [
        'Content-Type',
        'X-Requested-With',
        'X-Socket-ID',
        'X-CSRF-TOKEN',
        'Authorization',
        'Accept',
        'X-XSRF-TOKEN',
    ],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | This array contains the list of HTTP headers that should be exposed to
    | the browser when making cross-origin requests.
    |
    */
    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | This value sets the Access-Control-Max-Age header in seconds. This
    | determines how long the results of a preflight request can be cached.
    |
    */
    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    |
    | This value determines whether or not the response to the request can be
    | exposed when the credentials flag is true. When used as part of a
    | response to a preflight request, this indicates whether or not the
    | actual request can be made using credentials.
    |
    */
    'supports_credentials' => true,

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    |
    | Indicates which HTTP headers can be used during the actual request.
    |
    */
    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | Headers that are allowed to be exposed to the web server response.
    |
    */
    'exposed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | Indicates how long (in seconds) the results of a preflight request can be cached.
    |
    */
    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    |
    | Indicates whether the request can include user credentials like cookies,
    | HTTP authentication or client side SSL certificates.
    |
    */
    'supports_credentials' => true,

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:8000',
        'http://localhost:8080',
        'http://localhost',
        'http://127.0.0.1:8000',
        'http://127.0.0.1:3000',
        'http://127.0.0.1',
        'http://localhost/Legal-Case-Mngmnt/public',
        'http://localhost/Legal-Case-Mngmnt',
        env('APP_URL'),
        parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST) === 'localhost' ? 'http://localhost:*' : null,
        parse_url(env('APP_URL'), PHP_URL_HOST),
        parse_url(env('APP_URL'), PHP_URL_SCHEME).'://*.'.parse_url(env('APP_URL'), PHP_URL_HOST),
    ],

    'allowed_origins_patterns' => [
        '/^https?:\/\/(.+?\.)?'.preg_quote(parse_url(env('APP_URL'), PHP_URL_HOST)).'(:\d+)?$/',
        '/^https?:\/\/localhost(:\d+)?$/',
        '/^https?:\/\/127\.0\.0\.1(:\d+)?$/',
        '/^https?:\/\/.*\.test(:\d+)?$/',
    ],

    'allowed_headers' => [
        'Authorization',
        'Content-Type',
        'X-XSRF-TOKEN',
        'X-Requested-With',
        'X-Socket-ID',
        'X-CSRF-TOKEN',
        'X-Requested-With',
        'Accept',
        'Origin',
        'DNT',
        'User-Agent',
        'If-Modified-Since',
        'Cache-Control',
        'Content-Range',
        'Range',
        'X-Pusher-*',
        'X-Socket-Id',
    ],

    'exposed_headers' => [
        'Authorization',
        'X-Socket-ID',
        'X-CSRF-TOKEN',
        'X-XSRF-TOKEN',
    ],

    'max_age' => 60 * 60 * 24, // 24 hours
    'supports_credentials' => true,

];






