<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class RateLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $limit = 'default'): Response
    {
        $key = $this->resolveRequestSignature($request);
        $maxAttempts = $this->getMaxAttempts($request, $limit);
        $decayMinutes = $this->getDecayMinutes($limit);
        
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            throw new ThrottleRequestsException(
                'Too Many Attempts.',
                null,
                $this->getHeaders(
                    $maxAttempts,
                    $this->calculateRemainingAttempts($key, $maxAttempts),
                    $this->availableAt($key)
                )
            );
        }
        
        RateLimiter::increment($key, $decayMinutes * 60);
        
        $response = $next($request);
        
        return $this->addHeaders(
            $response,
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts, $maxAttempts)
        );
    }
    
    /**
     * Resolve the request signature.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        if ($user = Auth::user()) {
            return 'user:' . $user->id;
        }
        
        return 'ip:' . $request->ip();
    }
    
    /**
     * Get the maximum number of attempts allowed.
     */
    protected function getMaxAttempts(Request $request, string $limit): int
    {
        if ($user = Auth::user()) {
            return $this->getRoleBasedLimit($user->role, $limit);
        }
        
        return $this->getGuestLimit($limit);
    }
    
    /**
     * Get role-based rate limits.
     */
    protected function getRoleBasedLimit(string $role, string $limit): int
    {
        $limits = [
            'Admin' => [
                'default' => 1000,
                'api' => 500,
                'login' => 10,
                'upload' => 50,
            ],
            'Supervisor' => [
                'default' => 500,
                'api' => 300,
                'login' => 8,
                'upload' => 30,
            ],
            'Lawyer' => [
                'default' => 300,
                'api' => 200,
                'login' => 6,
                'upload' => 20,
            ],
        ];
        
        return $limits[$role][$limit] ?? 100;
    }
    
    /**
     * Get guest rate limits.
     */
    protected function getGuestLimit(string $limit): int
    {
        $limits = [
            'default' => 60,
            'api' => 30,
            'login' => 5,
            'upload' => 0, // No uploads for guests
        ];
        
        return $limits[$limit] ?? 30;
    }
    
    /**
     * Get the decay time in minutes.
     */
    protected function getDecayMinutes(string $limit): int
    {
        $decay = [
            'default' => 1, // 1 minute
            'api' => 1,
            'login' => 15, // 15 minutes for login attempts
            'upload' => 5, // 5 minutes for uploads
        ];
        
        return $decay[$limit] ?? 1;
    }
    
    /**
     * Calculate the remaining attempts.
     */
    protected function calculateRemainingAttempts(string $key, int $maxAttempts, int $retryAfter = null): int
    {
        return RateLimiter::retriesLeft($key, $maxAttempts);
    }
    
    /**
     * Get the time at which the key will be available again.
     */
    protected function availableAt(string $key): int
    {
        return RateLimiter::availableAt($key);
    }
    
    /**
     * Get the throttle headers.
     */
    protected function getHeaders(int $maxAttempts, int $remainingAttempts, int $retryAfter = null): array
    {
        $headers = [
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ];
        
        if ($retryAfter) {
            $headers['Retry-After'] = $retryAfter;
            $headers['X-RateLimit-Reset'] = $retryAfter;
        }
        
        return $headers;
    }
    
    /**
     * Add the throttle headers to the response.
     */
    protected function addHeaders(Response $response, int $maxAttempts, int $remainingAttempts): Response
    {
        $response->headers->add($this->getHeaders($maxAttempts, $remainingAttempts));
        
        return $response;
    }
}






