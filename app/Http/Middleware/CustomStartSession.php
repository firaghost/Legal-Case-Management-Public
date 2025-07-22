<?php

namespace App\Http\Middleware;

use Illuminate\Session\Middleware\StartSession as BaseStartSession;
use Illuminate\Http\Request;

class CustomStartSession extends BaseStartSession
{
    // Remove the shouldBlockSessionForRequest and getSession overrides to restore default session behavior
}






