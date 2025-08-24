<?php

return [
    App\Providers\AppServiceProvider::class,
    Illuminate\Broadcasting\BroadcastServiceProvider::class,
    
    // Module Service Providers
    App\Modules\User\UserServiceProvider::class,
    App\Modules\Product\ProductServiceProvider::class,
    App\Modules\Order\OrderServiceProvider::class,
    App\Modules\Admin\AdminServiceProvider::class,
    App\Modules\Support\SupportServiceProvider::class,
    App\Modules\Inventory\InventoryServiceProvider::class,
];
