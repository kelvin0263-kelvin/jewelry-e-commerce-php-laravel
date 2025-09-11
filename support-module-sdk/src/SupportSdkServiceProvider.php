<?php

namespace SupportModule\Sdk;

use Illuminate\Support\ServiceProvider;

class SupportSdkServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/support-sdk.php', 'support-sdk');

        $this->app->singleton(SupportClient::class, function ($app) {
            $config = $app['config']->get('support-sdk', []);
            return new SupportClient([
                'base_url' => $config['base_url'] ?? null,
                'token' => $config['token'] ?? null,
                'api_prefix' => $config['api_prefix'] ?? '/api',
                'timeout' => $config['timeout'] ?? 10,
                'debug' => (bool)($config['debug'] ?? false),
            ]);
        });

        // Backwards-compatible binding name
        $this->app->alias(SupportClient::class, 'support-sdk');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/support-sdk.php' => config_path('support-sdk.php'),
        ], 'support-sdk-config');
    }
}

