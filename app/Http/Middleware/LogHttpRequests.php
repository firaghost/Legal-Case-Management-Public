<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AuditLog;

class LogHttpRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip logging for certain paths (e.g., health checks, monitoring)
        if ($this->shouldSkipLogging($request->getPathInfo())) {
            return $next($request);
        }

        // Skip non-GET requests to log files to avoid recursion
        if (str_contains($request->getPathInfo(), '/logs') && $request->method() !== 'GET') {
            return $next($request);
        }

        // Skip API requests unless explicitly needed
        if (str_starts_with($request->getPathInfo(), '/api/') && 
            !in_array($request->getPathInfo(), $this->apiPathsToLog())) {
            return $next($request);
        }

        // Process the request first to get the response
        $response = $next($request);
        
        // Log the request after processing to ensure we have all data
        try {
            $this->logRequest($request);
        } catch (\Exception $e) {
            // Don't break the response if logging fails
            Log::error('Failed to log request: ' . $e->getMessage());
        }
        
        return $response;
    }
    
    /**
     * API paths that should be logged
     */
    protected function apiPathsToLog(): array
    {
        return [
            // Add specific API paths that should be logged
            // '/api/important-endpoint',
        ];
    }
    
    /**
     * Log the request details.
     */
    protected function logRequest(Request $request): void
    {
        try {
            $method = strtoupper($request->getMethod());
            $uri = $request->getPathInfo();
            $ip = $request->ip();
            $user = $request->user();
            $userId = $user ? $user->id : null;
            
            // Skip logging for certain paths (e.g., health checks, monitoring)
            if ($this->shouldSkipLogging($uri)) {
                Log::debug('Skipping audit logging for path: ' . $uri);
                return;
            }
            
            $context = [
                'method' => $method,
                'uri' => $uri,
                'ip' => $ip,
                'user_id' => $userId,
                'user_agent' => $request->userAgent(),
                'content_type' => $request->header('Content-Type'),
                'accept' => $request->header('Accept'),
                'authorization' => $request->header('Authorization') ? '***' : null,
                'query_params' => $request->query(),
                'request_body' => $this->filterSensitiveData($request->all()),
            ];
            
            Log::info("HTTP Request: {$method} {$uri}", $context);

            // Skip logging for specific paths
            $skipPaths = ['/admin/logs', '/api/logs', '/_debugbar'];
            if (in_array($uri, $skipPaths)) {
                return;
            }

            // Persist to audit_logs table
            AuditLog::create([
                'user_id' => $userId,
                'auditable_type' => 'http_request',
                'auditable_id' => 0,
                'action' => "{$method} {$uri}",
                'changes' => $this->filterSensitiveData($request->all()),
                'ip_address' => $ip,
                'user_agent' => $request->userAgent(),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to log request to audit log: ' . $e->getMessage(), [
                'exception' => $e,
                'uri' => $request->getPathInfo(),
                'method' => $request->method(),
            ]);
        }
    }
    
    /**
     * Log the response details.
     */
    protected function logResponse(Request $request, $response): void
    {
        $method = strtoupper($request->getMethod());
        $uri = $request->getPathInfo();
        $status = $response->getStatusCode();
        
        // Skip logging for certain paths
        if ($this->shouldSkipLogging($uri)) {
            return;
        }
        
        $context = [
            'method' => $method,
            'uri' => $uri,
            'status' => $status,
            'content_type' => $response->headers->get('Content-Type'),
            'response_time' => microtime(true) - LARAVEL_START,
        ];
        
        // Only log response body for non-200 responses or if debug is enabled
        if ($status >= 400 || config('app.debug')) {
            $content = $response->getContent();
            $json = json_decode($content, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                $context['response'] = $this->filterSensitiveData($json);
            } else {
                $context['response'] = $content;
            }
        }
        
        // Log at different levels based on status code
        if ($status >= 500) {
            Log::error("HTTP Response: {$method} {$uri} - {$status}", $context);
        } elseif ($status >= 400) {
            Log::warning("HTTP Response: {$method} {$uri} - {$status}", $context);
        } else {
            Log::info("HTTP Response: {$method} {$uri} - {$status}", $context);
        }
    }
    
    /**
     * Filter out sensitive data from logs.
     */
    protected function filterSensitiveData(array $data): array
    {
        $sensitiveKeys = [
            'password',
            'password_confirmation',
            'token',
            'api_token',
            'access_token',
            'refresh_token',
            'authorization',
            'card_number',
            'cvv',
            'expiry_date',
            'ssn',
            'social_security_number',
        ];
        
        array_walk_recursive($data, function (&$value, $key) use ($sensitiveKeys) {
            if (in_array(strtolower($key), $sensitiveKeys)) {
                $value = '***';
            }
        });
        
        return $data;
    }
    
    /**
     * Determine if the request should skip logging.
     */
    protected function shouldSkipLogging(string $uri): bool
    {
        // Disable path skipping to ensure universal logging
        return false;
    }
}






