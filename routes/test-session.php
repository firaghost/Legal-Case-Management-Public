<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/test-session', function () {
    // Return session and CSRF token info
    return response()->json([
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
        'csrf_token' => csrf_token(),
        'config' => [
            'session' => [
                'driver' => config('session.driver'),
                'cookie' => config('session.cookie'),
                'path' => config('session.path'),
                'domain' => config('session.domain'),
                'secure' => config('session.secure'),
                'same_site' => config('session.same_site'),
            ],
            'app' => [
                'env' => config('app.env'),
                'debug' => config('app.debug'),
                'url' => config('app.url'),
            ]
        ]
    ]);
});

Route::post('/test-session', function () {
    // Validate CSRF token manually
    $token = request()->input('_token') ?: request()->header('X-CSRF-TOKEN');
    
    if (!hash_equals(session()->token(), $token)) {
        return response()->json([
            'status' => 'error',
            'message' => 'CSRF token mismatch',
            'session_token' => session()->token(),
            'request_token' => $token,
            'headers' => request()->headers->all(),
            'cookies' => request()->cookies->all(),
            'session_data' => session()->all(),
        ], 419);
    }

    return response()->json([
        'status' => 'success',
        'message' => 'CSRF token is valid!',
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
    ]);
});






