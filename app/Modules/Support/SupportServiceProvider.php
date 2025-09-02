<?php

namespace App\Modules\Support;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Event;

// Events
use App\Modules\Support\Events\TicketCreated;
use App\Modules\Support\Events\TicketStatusChanged;
use App\Modules\Support\Events\TicketAssigned;
use App\Modules\Support\Events\TicketReplyAdded;
use App\Modules\Support\Events\TicketEscalated;
use App\Modules\Support\Events\TicketResolved;

// Listeners
use App\Modules\Support\Listeners\NotifyCustomerTicketCreated;
use App\Modules\Support\Listeners\NotifyAdminsNewTicket;
use App\Modules\Support\Listeners\UpdateTicketMetrics;
use App\Modules\Support\Listeners\LogTicketActivity;
use App\Modules\Support\Listeners\SendTicketAssignmentNotification;
use App\Modules\Support\Listeners\SendReplyNotification;

class SupportServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        TicketCreated::class => [
            NotifyCustomerTicketCreated::class,
            NotifyAdminsNewTicket::class,
            LogTicketActivity::class . '@handleTicketCreated',
            UpdateTicketMetrics::class . '@handleTicketCreated',
        ],
        TicketStatusChanged::class => [
            LogTicketActivity::class . '@handleTicketStatusChanged',
            UpdateTicketMetrics::class . '@handleTicketStatusChanged',
        ],
        TicketAssigned::class => [
            SendTicketAssignmentNotification::class,
            LogTicketActivity::class . '@handleTicketAssigned',
        ],
        TicketReplyAdded::class => [
            SendReplyNotification::class,
            LogTicketActivity::class . '@handleTicketReplyAdded',
        ],
        TicketEscalated::class => [
            LogTicketActivity::class . '@handleTicketEscalated',
        ],
        TicketResolved::class => [
            LogTicketActivity::class . '@handleTicketResolved',
            UpdateTicketMetrics::class . '@handleTicketResolved',
        ],
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        // Register services
        $this->app->singleton(
            \App\Modules\Support\Services\ChatQueueService::class,
            \App\Modules\Support\Services\ChatQueueService::class
        );

        $this->app->register(\App\Modules\Support\Providers\ChatObserverServiceProvider::class);

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load views
        $this->loadViewsFrom(__DIR__ . '/Views', 'support');
        
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        // Register routes
        Route::prefix('api')
        ->middleware('api')
        ->group(__DIR__.'/Routes/api.php');

        // Register event listeners
        $this->registerEventListeners();

        // Configure logging channel
        $this->configureLogging();
    }

    /**
     * Register event listeners for the Observer Pattern
     */
    protected function registerEventListeners(): void
    {
        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }
    }

    /**
     * Configure support module logging
     */
    protected function configureLogging(): void
    {
        $this->app['config']->set('logging.channels.support', [
            'driver' => 'daily',
            'path' => storage_path('logs/support.log'),
            'level' => 'info',
            'days' => 30,
        ]);
    }
}