<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DatabaseSecurity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 启用数据库查询日志记录
        DB::enableQueryLog();
        
        try {
            $response = $next($request);
            
            // 记录查询日志
            $this->logQueries($request);
            
            return $response;
        } catch (\Exception $e) {
            // 记录数据库相关错误
            $this->logDatabaseError($e, $request);
            throw $e;
        }
    }

    /**
     * Log database queries for security monitoring.
     */
    private function logQueries(Request $request): void
    {
        $queries = DB::getQueryLog();
        
        if (!empty($queries)) {
            $suspiciousQueries = $this->detectSuspiciousQueries($queries);
            
            if (!empty($suspiciousQueries)) {
                Log::warning('Suspicious database queries detected', [
                    'queries' => $suspiciousQueries,
                    'user_id' => auth()->id(),
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl(),
                    'timestamp' => now()
                ]);
            }
        }
    }

    /**
     * Detect potentially malicious database queries.
     */
    private function detectSuspiciousQueries(array $queries): array
    {
        $suspicious = [];
        
        foreach ($queries as $query) {
            $sql = strtolower($query['query']);
            
            // 检测SQL注入尝试
            $injectionPatterns = [
                '/union\s+select/i',
                '/drop\s+table/i',
                '/delete\s+from/i',
                '/update\s+.*\s+set/i',
                '/insert\s+into/i',
                '/alter\s+table/i',
                '/create\s+table/i',
                '/exec\s*\(/i',
                '/xp_cmdshell/i',
                '/sp_executesql/i',
                '/waitfor\s+delay/i',
                '/benchmark\s*\(/i',
                '/sleep\s*\(/i'
            ];
            
            foreach ($injectionPatterns as $pattern) {
                if (preg_match($pattern, $sql)) {
                    $suspicious[] = [
                        'query' => $query['query'],
                        'bindings' => $query['bindings'],
                        'time' => $query['time'],
                        'reason' => 'Potential SQL injection detected'
                    ];
                    break;
                }
            }
            
            // 检测异常查询时间
            if ($query['time'] > 5000) { // 超过5秒
                $suspicious[] = [
                    'query' => $query['query'],
                    'bindings' => $query['bindings'],
                    'time' => $query['time'],
                    'reason' => 'Unusually long query execution time'
                ];
            }
        }
        
        return $suspicious;
    }

    /**
     * Log database errors with security context.
     */
    private function logDatabaseError(\Exception $e, Request $request): void
    {
        $errorData = [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'timestamp' => now(),
            'queries' => DB::getQueryLog()
        ];

        // 根据错误类型记录不同级别的日志
        if ($e instanceof \Illuminate\Database\QueryException) {
            Log::error('Database Query Exception', $errorData);
        } elseif ($e instanceof \Illuminate\Database\ConnectionException) {
            Log::error('Database Connection Exception', $errorData);
        } else {
            Log::error('Database Related Error', $errorData);
        }
    }
}
