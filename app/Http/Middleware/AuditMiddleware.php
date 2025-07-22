<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log the incoming request
        $this->logRequest($request);
        
        $response = $next($request);
        
        // Log the response if needed
        $this->logResponse($request, $response);
        
        return $response;
    }
    
    /**
     * Log the incoming request
     */
    protected function logRequest(Request $request): void
    {
        // Only log certain actions (POST, PUT, DELETE, etc.)
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $data = [
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'action' => $request->method() . ' ' . $request->path(),
                'model' => $this->getModelFromRoute($request),
                'model_id' => $this->getModelIdFromRoute($request),
                'old_values' => null,
                'new_values' => $this->filterSensitiveData($request->all()),
                'url' => $request->fullUrl(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            AuditLog::create($data);
        }
    }
    
    /**
     * Log the response
     */
    protected function logResponse(Request $request, Response $response): void
    {
        // Log failed responses or specific status codes
        if ($response->getStatusCode() >= 400) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'action' => 'ERROR_RESPONSE',
                'model' => 'Response',
                'model_id' => null,
                'old_values' => null,
                'new_values' => [
                    'status_code' => $response->getStatusCode(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                ],
                'url' => $request->fullUrl(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    
    /**
     * Extract model name from route
     */
    protected function getModelFromRoute(Request $request): ?string
    {
        $route = $request->route();
        if (!$route) return null;
        
        $routeName = $route->getName();
        if (!$routeName) return null;
        
        // Extract model from route name (e.g., 'cases.store' -> 'Case')
        $parts = explode('.', $routeName);
        if (count($parts) > 0) {
            return ucfirst(str_singular($parts[0]));
        }
        
        return null;
    }
    
    /**
     * Extract model ID from route parameters
     */
    protected function getModelIdFromRoute(Request $request): ?int
    {
        $route = $request->route();
        if (!$route) return null;
        
        $parameters = $route->parameters();
        
        // Common parameter names for IDs
        $idParams = ['id', 'case', 'user', 'case_file', 'document'];
        
        foreach ($idParams as $param) {
            if (isset($parameters[$param])) {
                return is_object($parameters[$param]) ? $parameters[$param]->id : $parameters[$param];
            }
        }
        
        return null;
    }
    
    /**
     * Filter sensitive data from request
     */
    protected function filterSensitiveData(array $data): array
    {
        $sensitiveFields = ['password', 'password_confirmation', 'token', 'secret', 'key'];
        
        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '[FILTERED]';
            }
        }
        
        return $data;
    }
}






