<?php

namespace App\Modules\Support\Observers;

use App\Modules\Support\Contracts\ChatObserverInterface;
use App\Modules\Support\Models\ChatQueue;
use App\Modules\Support\Models\AgentStatus;
use Illuminate\Support\Facades\Log;

/**
 * Observer that handles database updates related to chat events
 */
class DatabaseObserver implements ChatObserverInterface
{
    /**
     * Handle chat event updates
     */
    public function update(string $event, array $data): void
    {
        Log::info("DatabaseObserver: Handling {$event}", ['event' => $event]);

        try {
            switch ($event) {
                case 'conversation_terminated':
                    $this->handleConversationTerminated($data);
                    break;

                case 'conversation_activated':
                    $this->handleConversationActivated($data);
                    break;

                case 'agent_status_changed':
                    $this->handleAgentStatusChanged($data);
                    break;

                case 'message_sent':
                    $this->handleMessageSent($data);
                    break;

                default:
                    Log::debug("DatabaseObserver: No handler for event {$event}");
                    break;
            }
        } catch (\Exception $e) {
            Log::error("DatabaseObserver: Error handling {$event}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle conversation terminated event
     */
    private function handleConversationTerminated(array $data): void
    {
        if (!isset($data['conversation'])) {
            return;
        }

        $conversation = $data['conversation'];

        // Only update if we have a proper Conversation model instance
        if (!($conversation instanceof \App\Modules\Support\Models\Conversation)) {
            Log::debug('DatabaseObserver: Skipping termination update for non-Conversation instance');
            return;
        }

        // Update conversation timestamps
        $conversation->update([
            'ended_at' => now(),
            'end_reason' => $data['reason'] ?? 'unknown'
        ]);

        // Update queue status
        $queueItem = ChatQueue::where('conversation_id', $conversation->id)->first();
        if ($queueItem) {
            $queueItem->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
        }

        // Update agent status (reduce active chats count)
        if ($conversation->assigned_agent_id) {
            $agentStatus = AgentStatus::where('user_id', $conversation->assigned_agent_id)->first();
            if ($agentStatus && $agentStatus->current_active_chats > 0) {
                $agentStatus->decrement('current_active_chats');
                $agentStatus->touch('last_activity_at');
            }
        }

        Log::info('DatabaseObserver: Conversation termination processed', [
            'conversation_id' => $conversation->id,
            'agent_id' => $conversation->assigned_agent_id
        ]);
    }

    /**
     * Handle conversation activated event
     */
    private function handleConversationActivated(array $data): void
    {
        if (!isset($data['conversation'])) {
            return;
        }

        $conversation = $data['conversation'];

        // Only update if we have a proper Conversation model instance
        if (!($conversation instanceof \App\Modules\Support\Models\Conversation)) {
            Log::debug('DatabaseObserver: Skipping activation update for non-Conversation instance');
            return;
        }

        // Update conversation
        $conversation->update([
            'status' => 'active',
            'started_at' => now()
        ]);

        // Update queue status
        $queueItem = ChatQueue::where('conversation_id', $conversation->id)->first();
        if ($queueItem) {
            $queueItem->update([
                'status' => 'assigned',
                'assigned_at' => now()
            ]);
        }

        // Update agent status (increment active chats count)
        if ($conversation->assigned_agent_id) {
            $agentStatus = AgentStatus::where('user_id', $conversation->assigned_agent_id)->first();
            if ($agentStatus) {
                $agentStatus->increment('current_active_chats');
                $agentStatus->touch('last_activity_at');
            }
        }

        Log::info('DatabaseObserver: Conversation activation processed', [
            'conversation_id' => $conversation->id,
            'agent_id' => $conversation->assigned_agent_id
        ]);
    }

    /**
     * Handle agent status changed event
     */
    private function handleAgentStatusChanged(array $data): void
    {
        if (!isset($data['agent_id'], $data['new_status'])) {
            return;
        }

        $agentStatus = AgentStatus::where('user_id', $data['agent_id'])->first();
        if ($agentStatus) {
            $agentStatus->update([
                'status' => $data['new_status'],
                'status_changed_at' => now(),
                'last_activity_at' => now()
            ]);

            Log::info('DatabaseObserver: Agent status updated', [
                'agent_id' => $data['agent_id'],
                'old_status' => $data['old_status'],
                'new_status' => $data['new_status']
            ]);
        }
    }

    /**
     * Handle message sent event
     */
    private function handleMessageSent(array $data): void
    {
        if (!isset($data['conversation'])) {
            return;
        }

        // Only update if we have a proper Conversation model instance
        if ($data['conversation'] instanceof \App\Modules\Support\Models\Conversation) {
            // Touch the conversation to update its timestamp
            $data['conversation']->touch();

            Log::debug('DatabaseObserver: Conversation timestamp updated for message', [
                'conversation_id' => $data['conversation']->id
            ]);
        } else {
            Log::debug('DatabaseObserver: Skipping conversation update for non-Conversation instance', [
                'conversation_type' => gettype($data['conversation']),
                'conversation_class' => is_object($data['conversation']) ? get_class($data['conversation']) : 'not_object'
            ]);
        }
    }
}
