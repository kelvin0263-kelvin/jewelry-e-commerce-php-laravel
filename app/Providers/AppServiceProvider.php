<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use App\Modules\User\Services\UserService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton('user-service', function ($app) {
            return new UserService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(base_path('app/Modules/User/Views'), 'user');

        // Named rate limiters for chat/tickets (per user and IP)
        RateLimiter::for('chat-send', function (Request $request) {
            $key = 'chat-send:' . ($request->user()?->id ?? 'guest') . '|' . $request->ip();
            return [
                Limit::perMinute(30)->by($key),
            ];
        });

        RateLimiter::for('chat-start', function (Request $request) {
            $key = 'chat-start:' . ($request->user()?->id ?? 'guest') . '|' . $request->ip();
            return [
                Limit::perMinute(5)->by($key),
            ];
        });

        RateLimiter::for('ticket-post', function (Request $request) {
            $key = 'ticket-post:' . ($request->user()?->id ?? 'guest') . '|' . $request->ip();
            return [
                Limit::perMinute(10)->by($key),
            ];
        });

        RateLimiter::for('admin-chat', function (Request $request) {
            $key = 'admin-chat:' . ($request->user()?->id ?? 'guest') . '|' . $request->ip();
            return [
                Limit::perMinute(120)->by($key),
            ];
        });
    }


}
