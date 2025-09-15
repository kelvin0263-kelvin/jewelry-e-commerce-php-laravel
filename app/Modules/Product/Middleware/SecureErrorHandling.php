<?php
/**
 * Author: SIA XIAO HUI
 * Date: 2025-09-15
 */

namespace App\Modules\Product\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecureErrorHandling
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (\Exception $e) {
            return $this->handleException($e, $request);
        }
    }

    /**
     * Handle exceptions with secure error reporting.
     */
    private function handleException(\Exception $e, Request $request): Response
    {
        
        $this->logError($e, $request);

        
        if (config('app.debug')) {
            
            return $this->getDebugResponse($e);
        } else {
            
            return $this->getProductionResponse($e);
        }
    }

    /**
     * Log error with security context.
     */
    private function logError(\Exception $e, Request $request): void
    {
        $errorData = [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'timestamp' => now(),
            'session_id' => session()->getId()
        ];

        
        if ($e instanceof \Illuminate\Database\QueryException) {
            Log::error('Product Module Database Error', $errorData);
        } elseif ($e instanceof \Illuminate\Validation\ValidationException) {
            Log::warning('Product Module Validation Error', $errorData);
        } elseif ($e instanceof \Illuminate\Auth\AuthenticationException) {
            Log::warning('Product Module Authentication Error', $errorData);
        } elseif ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
            Log::warning('Product Module Authorization Error', $errorData);
        } else {
            Log::error('Product Module Application Error', $errorData);
        }
    }

    /**
     * Get debug response for development.
     */
    private function getDebugResponse(\Exception $e): Response
    {
        return response()->json([
            'error' => true,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }

    /**
     * Get production response hiding sensitive information.
     */
    private function getProductionResponse(\Exception $e): Response
    {
        
        $safeMessage = $this->filterSensitiveInfo($e->getMessage());
        
        
        if ($e instanceof \Illuminate\Database\QueryException) {
            $message = 'A database error occurred. Please try again later.';
        } elseif ($e instanceof \Illuminate\Validation\ValidationException) {
            $message = 'Invalid data provided. Please check your input.';
        } elseif ($e instanceof \Illuminate\Auth\AuthenticationException) {
            $message = 'Authentication required.';
        } elseif ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
            $message = 'You do not have permission to perform this action.';
        } else {
            $message = 'An unexpected error occurred. Please try again later.';
        }

        return response()->json([
            'error' => true,
            'message' => $message,
            'error_id' => uniqid('err_', true) 
        ], 500);
    }

    /**
     * Filter sensitive information from error messages.
     */
    private function filterSensitiveInfo(string $message): string
    {
        
        $message = preg_replace('/mysql:\/\/[^@]+@/', 'mysql://***:***@', $message);
        
        
        $message = preg_replace('/\/[^\s]+\.php/', '/***.php', $message);
        
        
        $sensitivePatterns = [
            '/password[=:]\s*[^\s]+/i',
            '/secret[=:]\s*[^\s]+/i',
            '/key[=:]\s*[^\s]+/i',
            '/token[=:]\s*[^\s]+/i'
        ];
        
        foreach ($sensitivePatterns as $pattern) {
            $message = preg_replace($pattern, '***', $message);
        }
        
        return $message;
    }
}
