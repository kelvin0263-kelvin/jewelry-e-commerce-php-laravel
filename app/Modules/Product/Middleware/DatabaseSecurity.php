<?php
/**
 * Author: SIA XIAO HUI
 * Date: 2025-09-15
 */

namespace App\Modules\Product\Middleware;

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
        
        DB::enableQueryLog();
        
        try {
            $response = $next($request);
            
            
            $this->logQueries($request);
            
            return $response;
        } catch (\Exception $e) {
            
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
                Log::warning('Product Module Suspicious database queries detected', [
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

        
        if ($e instanceof \Illuminate\Database\QueryException) {
            Log::error('Product Module Database Query Exception', $errorData);
        } elseif ($e instanceof \Illuminate\Database\ConnectionException) {
            Log::error('Product Module Database Connection Exception', $errorData);
        } else {
            Log::error('Product Module Database Related Error', $errorData);
        }
    }
}
