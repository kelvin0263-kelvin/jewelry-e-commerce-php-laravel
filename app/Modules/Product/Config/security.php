<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Product Module Security Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains security-related configuration for the Product Module.
    | It includes settings for input validation, rate limiting, and security
    | monitoring.
    |
    */

    'input_validation' => [
        'max_search_length' => 100,
        'max_description_length' => 1000,
        'max_name_length' => 255,
        'max_sku_length' => 50,
    ],

    'rate_limiting' => [
        'product_management' => '30,1', // 30 requests per minute
        'product_search' => '60,1',     // 60 requests per minute
        'product_review' => '10,1',     // 10 requests per minute
    ],

    'security_monitoring' => [
        'log_suspicious_queries' => true,
        'log_security_events' => true,
        'max_query_time' => 5000, // milliseconds
        'enable_input_sanitization' => true,
    ],

    'sql_injection_patterns' => [
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
    ],

    'sensitive_patterns' => [
        '/password[=:]\s*[^\s]+/i',
        '/secret[=:]\s*[^\s]+/i',
        '/key[=:]\s*[^\s]+/i',
        '/token[=:]\s*[^\s]+/i'
    ],
];
