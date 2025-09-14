<?php
/**
 * Author: TAN CHUN KEAT
 * Date: 2025-09-15
 */
namespace App\Modules\Support\Contracts;

/**
 * Subject interface for chat events (Observable)
 */
interface ChatSubjectInterface
{
    /**
     * Attach an observer
     */
    public function attach(ChatObserverInterface $observer): void;

    /**
     * Detach an observer
     */
    public function detach(ChatObserverInterface $observer): void;

    /**
     * Notify all observers
     */
    public function notify(string $event, array $data): void;
}
