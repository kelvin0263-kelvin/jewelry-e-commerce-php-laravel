<?php

return [
    'base_url' => env('SUPPORT_API_BASE_URL', config('app.url')),
    'token' => env('SUPPORT_API_TOKEN'),
    'api_prefix' => env('SUPPORT_API_PREFIX', '/api'),
    'timeout' => (int) env('SUPPORT_API_TIMEOUT', 10),
    'debug' => (bool) env('SUPPORT_API_DEBUG', false),
];

