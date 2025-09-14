<?php
/**
 * Author: TAN CHUN KEAT
 * Date: 2025-09-15
 */
namespace App\Modules\Support\Contracts;

/**
 * Observer interface for chat events
 */
interface ChatObserverInterface
{

    public function update(string $event, array $data): void;
}
