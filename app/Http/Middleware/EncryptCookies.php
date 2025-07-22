<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected array $except = [
        // The XSRF token cookie should not be encrypted so JavaScript can read it.
        'XSRF-TOKEN',
    ];
}






