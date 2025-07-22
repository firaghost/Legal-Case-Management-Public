<?php

use Illuminate\Support\Facades\Route;

Route::get('/test-csrf', function () {
    return response()->json([
        'session_id' => session()->getId(),
        'token' => csrf_token(),
        'session' => session()->all(),
        'cookies' => request()->cookies->all(),
        'headers' => [
            'x-xsrf-token' => request()->header('x-xsrf-token'),
            'x-csrf-token' => request()->header('x-csrf-token'),
        ],
        'session_config' => [
            'driver' => config('session.driver'),
            'lifetime' => config('session.lifetime'),
            'expire_on_close' => config('session.expire_on_close'),
            'encrypt' => config('session.encrypt'),
            'cookie' => config('session.cookie'),
            'path' => config('session.path'),
            'domain' => config('session.domain'),
            'secure' => config('session.secure'),
            'http_only' => config('session.http_only'),
            'same_site' => config('session.same_site'),
        ]
    ]);
})->name('test.csrf');

Route::post('/test-csrf', function () {
    return response()->json([
        'status' => 'CSRF token validated',
        'session_id' => session()->getId(),
        'input' => request()->all(),
    ]);
});






