<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Error Handling Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for secure error handling
    | in the application.
    |
    */

    'production' => [
        'hide_errors' => true,
        'log_errors' => true,
        'error_message' => 'An unexpected error occurred. Please try again later.',
        'database_error_message' => 'A database error occurred. Please try again later.',
        'validation_error_message' => 'Invalid data provided. Please check your input.',
        'authentication_error_message' => 'Authentication required.',
        'authorization_error_message' => 'You do not have permission to perform this action.',
    ],

    'development' => [
        'hide_errors' => false,
        'log_errors' => true,
        'show_stack_trace' => true,
        'show_file_paths' => true,
    ],

    'sensitive_patterns' => [
        'database_credentials' => '/mysql:\/\/[^@]+@/',
        'file_paths' => '/\/[^\s]+\.php/',
        'passwords' => '/password[=:]\s*[^\s]+/i',
        'secrets' => '/secret[=:]\s*[^\s]+/i',
        'keys' => '/key[=:]\s*[^\s]+/i',
        'tokens' => '/token[=:]\s*[^\s]+/i',
    ],

    'log_levels' => [
        'database_errors' => 'error',
        'validation_errors' => 'warning',
        'authentication_errors' => 'warning',
        'authorization_errors' => 'warning',
        'file_upload_errors' => 'error',
        'general_errors' => 'error',
    ],

    'security_context' => [
        'include_user_id' => true,
        'include_ip_address' => true,
        'include_user_agent' => true,
        'include_url' => true,
        'include_session_id' => true,
        'include_timestamp' => true,
    ],
];

