<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\Store;

class PreventSessionRegeneration
{
    protected $session;

    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    public function handle(Request $request, Closure $next)
    {
        if ($request->is('admin/cases/create')) {
            // Prevent session from being regenerated
            if ($this->session->isStarted()) {
                $this->session->migrate(false);
            }
        }
        
        return $next($request);
    }
}






