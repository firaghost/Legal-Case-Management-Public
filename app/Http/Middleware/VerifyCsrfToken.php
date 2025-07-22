<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     */
    protected bool $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        // Removed 'lawyer/cases' and 'lawyer/cases/*' to re-enable CSRF protection
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->isReading($request) ||
            $this->runningUnitTests() ||
            $this->inExceptArray($request) ||
            $this->tokensMatch($request)) {
            return $this->addCookieToResponse($request, $next($request));
        }

        // Log token mismatch details for debugging
        if (app()->environment('local')) {
            \Log::debug('CSRF Token Mismatch', [
                'has_token' => $request->session()->has('_token'),
                'session_token' => $request->session()->token(),
                'token_from_request' => $request->input('_token') ?: $request->header('X-CSRF-TOKEN'),
                'session_id' => $request->session()->getId(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'cookies' => $request->cookies->all(),
            ]);
        }

        throw new TokenMismatchException('CSRF token mismatch.');
    }

    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        $token = $this->getTokenFromRequest($request);

        if (! $token) {
            return false;
        }

        $sessionToken = $request->session()->token();

        $isValid = hash_equals($sessionToken, $token);

        if (! $isValid) {
            \Log::warning('CSRF token mismatch', [
                'has_token' => $request->session()->has('_token'),
                'session_token' => $request->session()->token(),
                'token' => $token,
                'session_id' => $request->session()->getId(),
                'url' => $request->fullUrl(),
            ]);
        }

        return $isValid;
    }

    /**
     * Get the CSRF token from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function getTokenFromRequest($request)
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');

        if (! $token && $header = $request->header('X-XSRF-TOKEN')) {
            $token = $this->encrypter->decrypt($header, static::serialized());
        }

        return $token;
    }
}






