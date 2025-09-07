<?php

namespace App\Modules\Support\Providers;

use App\Modules\Support\Services\ChatEventManager;
use App\Modules\Support\Observers\BroadcastObserver;
use App\Modules\Support\Observers\DatabaseObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

/**
 * Service Provider for Chat Observer Pattern
 */
class ChatObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register(): void
    {
        // Register ChatEventManager as singleton
        $this->app->singleton(ChatEventManager::class, function ($app) {
            return ChatEventManager::getInstance();
        });

        // Register individual observers
        $this->app->singleton(BroadcastObserver::class);
        $this->app->singleton(DatabaseObserver::class);
        
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        $this->registerObservers();
    }

    /**
     * Register all observers with the ChatEventManager
     */
    private function registerObservers(): void
    {
        try {
            $eventManager = $this->app->make(ChatEventManager::class);

            // Register all observers
            $observers = [
                BroadcastObserver::class,
                DatabaseObserver::class,
            ];

            foreach ($observers as $observerClass) {
                $observer = $this->app->make($observerClass);
                $eventManager->attach($observer);
                
                Log::info("ChatObserverServiceProvider: Registered {$observerClass}");
            }

            Log::info('ChatObserverServiceProvider: All observers registered', [
                'total_observers' => count($observers)
            ]);

        } catch (\Exception $e) {
            Log::error('ChatObserverServiceProvider: Failed to register observers', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Get the services provided by the provider
     */
    public function provides(): array
    {
        return [
            ChatEventManager::class,
            BroadcastObserver::class,
            DatabaseObserver::class,
        ];
    }
}
