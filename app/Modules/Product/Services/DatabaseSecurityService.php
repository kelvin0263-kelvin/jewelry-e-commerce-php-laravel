<?php

namespace App\Modules\Product\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class DatabaseSecurityService
{
    /**
     * Execute secure database operations with comprehensive error handling.
     */
    public static function secureTransaction(callable $callback, array $context = [])
    {
        try {
            return DB::transaction($callback);
        } catch (QueryException $e) {
            self::logDatabaseError($e, $context);
            throw new \Exception('Database operation failed. Please try again.');
        } catch (\Exception $e) {
            self::logUnexpectedError($e, $context);
            throw new \Exception('An unexpected error occurred. Please try again.');
        }
    }

    /**
     * Validate database query parameters to prevent injection.
     */
    public static function validateQueryParameters(array $params): bool
    {
        foreach ($params as $param) {
            if (is_string($param)) {
                // Get SQL injection patterns from config
                $dangerousPatterns = config('product.security.sql_injection_patterns', [
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
                    '/sleep\s*\(/i',
                    '/--/',
                    '/\/\*/',
                    '/\*\//',
                    '/\x00/',
                    '/\x1a/'
                ]);

                foreach ($dangerousPatterns as $pattern) {
                    if (preg_match($pattern, $param)) {
                        Log::warning('Potentially dangerous query parameter detected', [
                            'parameter' => $param,
                            'pattern' => $pattern,
                            'user_id' => auth()->id(),
                            'ip' => request()->ip()
                        ]);
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Sanitize database input to prevent injection.
     */
    public static function sanitizeInput($input): string
    {
        if (!is_string($input)) {
            return '';
        }

        // Remove null bytes and control characters
        $input = str_replace(["\x00", "\x1a"], '', $input);
        
        // Remove SQL comment patterns
        $input = preg_replace('/--.*$/m', '', $input);
        $input = preg_replace('/\/\*.*?\*\//s', '', $input);
        
        // Escape special characters
        $input = addslashes($input);
        
        // Limit length to prevent buffer overflow
        $input = mb_substr($input, 0, 1000, 'UTF-8');
        
        return $input;
    }

    /**
     * Validate data types and constraints.
     */
    public static function validateDataTypes(array $data, array $rules): bool
    {
        foreach ($rules as $field => $constraints) {
            if (!isset($data[$field])) {
                continue;
            }

            $value = $data[$field];

            // Type validation
            if (isset($constraints['type'])) {
                switch ($constraints['type']) {
                    case 'string':
                        if (!is_string($value)) {
                            return false;
                        }
                        break;
                    case 'integer':
                        if (!is_int($value) && !ctype_digit($value)) {
                            return false;
                        }
                        break;
                    case 'numeric':
                        if (!is_numeric($value)) {
                            return false;
                        }
                        break;
                    case 'array':
                        if (!is_array($value)) {
                            return false;
                        }
                        break;
                }
            }

            // Length validation
            if (isset($constraints['max_length']) && is_string($value)) {
                if (mb_strlen($value, 'UTF-8') > $constraints['max_length']) {
                    return false;
                }
            }

            // Range validation
            if (isset($constraints['min']) && is_numeric($value)) {
                if ($value < $constraints['min']) {
                    return false;
                }
            }

            if (isset($constraints['max']) && is_numeric($value)) {
                if ($value > $constraints['max']) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Log database errors with security context.
     */
    private static function logDatabaseError(QueryException $e, array $context = []): void
    {
        Log::error('Database Security Error', array_merge([
            'error' => $e->getMessage(),
            'sql_state' => $e->getSqlState(),
            'error_code' => $e->getCode(),
            'sql' => $e->getSql(),
            'bindings' => $e->getBindings(),
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'timestamp' => now()
        ], $context));
    }

    /**
     * Log unexpected errors with security context.
     */
    private static function logUnexpectedError(\Exception $e, array $context = []): void
    {
        Log::error('Database Unexpected Error', array_merge([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'timestamp' => now()
        ], $context));
    }

    /**
     * Check for suspicious database activity.
     */
    public static function detectSuspiciousActivity(): bool
    {
        $queries = DB::getQueryLog();
        
        foreach ($queries as $query) {
            $sql = strtolower($query['query']);
            
            // Check for suspicious patterns
            $suspiciousPatterns = [
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

            foreach ($suspiciousPatterns as $pattern) {
                if (preg_match($pattern, $sql)) {
                    Log::critical('Suspicious database activity detected', [
                        'query' => $query['query'],
                        'bindings' => $query['bindings'],
                        'time' => $query['time'],
                        'user_id' => auth()->id(),
                        'ip' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'url' => request()->fullUrl(),
                        'timestamp' => now()
                    ]);
                    return true;
                }
            }
        }

        return false;
    }
}


