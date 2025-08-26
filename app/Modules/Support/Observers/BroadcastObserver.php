<?php

namespace App\Modules\Support\Observers;

use App\Modules\Support\Contracts\ChatObserverInterface;
use App\Modules\Support\Events\MessageSent;
use App\Modules\Support\Events\ConversationTerminated;
use Illuminate\Support\Facades\Log;

/**
 * Observer that handles real-time broadcasting of chat events
 */
class BroadcastObserver implements ChatObserverInterface
{
    /**
     * Handle chat event updates
     */
    public function update(string $event, array $data): void
    {
        Log::info("BroadcastObserver: Handling {$event}", ['event' => $event]);

        try {
            switch ($event) {
                case 'message_sent':
                    $this->handleMessageSent($data);
                    break;

                case 'conversation_terminated':
                    $this->handleConversationTerminated($data);
                    break;

                case 'conversation_status_changed':
                    $this->handleConversationStatusChanged($data);
                    break;

                default:
                    Log::debug("BroadcastObserver: No handler for event {$event}");
                    break;
            }
        } catch (\Exception $e) {
            Log::error("BroadcastObserver: Error handling {$event}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle message sent event
     */
    private function handleMessageSent(array $data): void
    {
        if (isset($data['message'])) {
            // Only broadcast if we have a proper Message model instance
            if ($data['message'] instanceof \App\Modules\Support\Models\Message) {
                broadcast(new MessageSent($data['message']));
                Log::info('BroadcastObserver: Message broadcast sent', [
                    'message_id' => $data['message']->id,
                    'conversation_id' => $data['message']->conversation_id
                ]);
            } else {
                Log::debug('BroadcastObserver: Skipping broadcast for non-Message instance', [
                    'message_type' => gettype($data['message']),
                    'message_class' => is_object($data['message']) ? get_class($data['message']) : 'not_object'
                ]);
            }
        }
    }

    /**
     * Handle conversation terminated event
     */
    private function handleConversationTerminated(array $data): void
    {
        if (isset($data['conversation'], $data['terminated_by'])) {
            // Only broadcast if we have a proper Conversation model instance
            if ($data['conversation'] instanceof \App\Modules\Support\Models\Conversation) {
                broadcast(new ConversationTerminated(
                    $data['conversation'],
                    $data['terminated_by'],
                    $data['reason'] ?? null
                ));
                
                Log::info('BroadcastObserver: Conversation termination broadcast sent', [
                    'conversation_id' => $data['conversation']->id,
                    'terminated_by' => $data['terminated_by']
                ]);
            } else {
                Log::debug('BroadcastObserver: Skipping broadcast for non-Conversation instance', [
                    'conversation_type' => gettype($data['conversation']),
                    'conversation_class' => is_object($data['conversation']) ? get_class($data['conversation']) : 'not_object'
                ]);
            }
        }
    }

    /**
     * Handle conversation status changed event
     */
    private function handleConversationStatusChanged(array $data): void
    {
        // If conversation was terminated, broadcast termination event
        if (isset($data['new_status']) && in_array($data['new_status'], ['completed', 'abandoned'])) {
            $terminatedBy = $data['new_status'] === 'completed' ? 'admin' : 'customer';
            $reason = $data['new_status'] === 'completed' ? 'resolved' : 'abandoned';
            
            $this->handleConversationTerminated([
                'conversation' => $data['conversation'],
                'terminated_by' => $terminatedBy,
                'reason' => $reason
            ]);
        }
    }
}
