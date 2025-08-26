<?php

namespace App\Modules\Support\Contracts;

/**
 * Subject interface for chat events (Observable)
 */
interface ChatSubjectInterface
{
    /**
     * Attach an observer
     * 
     * @param ChatObserverInterface $observer
     * @return void
     */
    public function attach(ChatObserverInterface $observer): void;

    /**
     * Detach an observer
     * 
     * @param ChatObserverInterface $observer
     * @return void
     */
    public function detach(ChatObserverInterface $observer): void;

    /**
     * Notify all observers
     * 
     * @param string $event The event type
     * @param array $data The event data
     * @return void
     */
    public function notify(string $event, array $data): void;
}
