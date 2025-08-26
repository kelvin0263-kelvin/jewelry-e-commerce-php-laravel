<?php

namespace App\Modules\Support\Contracts;

/**
 * Observer interface for chat events
 */
interface ChatObserverInterface
{
    /**
     * Handle chat event updates
     * 
     * @param string $event The event type
     * @param array $data The event data
     * @return void
     */
    public function update(string $event, array $data): void;
}
