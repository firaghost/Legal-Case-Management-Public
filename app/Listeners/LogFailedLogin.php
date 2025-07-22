<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use App\Services\AuditLogService;

class LogFailedLogin
{
    public function handle(Failed $event)
    {
        $email = $event->credentials['email'] ?? 'unknown';
        $request = request();
        
        AuditLogService::log(
            'LOGIN_FAILED',
            'User',
            null,
            [
                'email' => $email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'time' => now()->toDateTimeString()
            ]
        );
    }
}






