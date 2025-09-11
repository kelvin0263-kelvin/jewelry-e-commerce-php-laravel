<?php

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
        // 记录详细错误信息到日志
        $this->logError($e, $request);

        // 根据环境返回适当的错误响应
        if (config('app.debug')) {
            // 开发环境：显示详细错误信息
            return $this->getDebugResponse($e);
        } else {
            // 生产环境：隐藏敏感信息
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

        // 根据错误类型选择日志级别
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
        // 过滤敏感信息
        $safeMessage = $this->filterSensitiveInfo($e->getMessage());
        
        // 根据错误类型返回适当的用户友好消息
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
            'error_id' => uniqid('err_', true) // 用于追踪错误
        ], 500);
    }

    /**
     * Filter sensitive information from error messages.
     */
    private function filterSensitiveInfo(string $message): string
    {
        // 移除数据库连接信息
        $message = preg_replace('/mysql:\/\/[^@]+@/', 'mysql://***:***@', $message);
        
        // 移除文件路径信息
        $message = preg_replace('/\/[^\s]+\.php/', '/***.php', $message);
        
        // 移除敏感配置信息
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
