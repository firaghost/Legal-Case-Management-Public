<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\AuditLogService;

class LogSuccessfulLogin
{
    public function handle(Login $event)
    {
        $user = $event->user;
        $request = request();
        
        AuditLogService::log(
            'LOGIN_SUCCESS',
            get_class($user),
            $user->id,
            [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'time' => now()->toDateTimeString()
            ]
        );
    }
}






