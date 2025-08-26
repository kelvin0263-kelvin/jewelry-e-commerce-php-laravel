<?php

namespace App\Modules\Support\Traits;

use App\Modules\Support\Services\ChatEventManager;

/**
 * Trait for controllers and services that need to emit chat events
 */
trait EmitsChatEvents
{
    /**
     * Get the chat event manager instance
     */
    protected function getChatEventManager(): ChatEventManager
    {
        return app(ChatEventManager::class);
    }

    /**
     * Emit a message sent event
     */
    protected function emitMessageSent($message, $conversation): void
    {
        $this->getChatEventManager()->emitMessageSent($message, $conversation);
    }

    /**
     * Emit a conversation status changed event
     */
    protected function emitConversationStatusChanged($conversation, string $oldStatus, string $newStatus): void
    {
        $this->getChatEventManager()->emitConversationStatusChanged($conversation, $oldStatus, $newStatus);
    }

    /**
     * Emit a conversation terminated event
     */
    protected function emitConversationTerminated($conversation, string $terminatedBy, ?string $reason = null): void
    {
        $this->getChatEventManager()->emitConversationTerminated($conversation, $terminatedBy, $reason);
    }

    /**
     * Emit a conversation activated event
     */
    protected function emitConversationActivated($conversation, $agent): void
    {
        $this->getChatEventManager()->emitConversationActivated($conversation, $agent);
    }

    /**
     * Emit a queue position changed event
     */
    protected function emitQueuePositionChanged(int $conversationId, int $newPosition, int $estimatedWait): void
    {
        $this->getChatEventManager()->emitQueuePositionChanged($conversationId, $newPosition, $estimatedWait);
    }

    /**
     * Emit an agent status changed event
     */
    protected function emitAgentStatusChanged(int $agentId, string $oldStatus, string $newStatus): void
    {
        $this->getChatEventManager()->emitAgentStatusChanged($agentId, $oldStatus, $newStatus);
    }
}
