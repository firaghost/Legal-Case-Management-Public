<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Services\AuditLogService;

class LogLogout
{
    public function handle(Logout $event)
    {
        $user = $event->user;
        if (!$user) return;
        
        $request = request();
        
        AuditLogService::log(
            'LOGOUT',
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






