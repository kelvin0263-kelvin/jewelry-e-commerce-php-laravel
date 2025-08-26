<?php

namespace App\Modules\Support\Services;

use App\Modules\Support\Contracts\ChatObserverInterface;
use App\Modules\Support\Contracts\ChatSubjectInterface;
use Illuminate\Support\Facades\Log;
use SplObjectStorage;

/**
 * Chat Event Manager - Implements Observer Pattern as Subject
 * Manages all chat-related events and notifies observers
 */
class ChatEventManager implements ChatSubjectInterface
{
    /**
     * @var SplObjectStorage Collection of observers
     */
    private SplObjectStorage $observers;

    /**
     * @var array Event history for debugging
     */
    private array $eventHistory = [];

    /**
     * @var static Singleton instance
     */
    private static ?ChatEventManager $instance = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->observers = new SplObjectStorage();
        Log::info('ChatEventManager initialized');
    }

    /**
     * Get singleton instance
     */
    public static function getInstance(): ChatEventManager
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Attach an observer
     */
    public function attach(ChatObserverInterface $observer): void
    {
        $this->observers->attach($observer);
        
        Log::info('Observer attached to ChatEventManager', [
            'observer_class' => get_class($observer),
            'total_observers' => $this->observers->count()
        ]);
    }

    /**
     * Detach an observer
     */
    public function detach(ChatObserverInterface $observer): void
    {
        $this->observers->detach($observer);
        
        Log::info('Observer detached from ChatEventManager', [
            'observer_class' => get_class($observer),
            'total_observers' => $this->observers->count()
        ]);
    }

    /**
     * Notify all observers
     */
    public function notify(string $event, array $data): void
    {
        // Add to event history
        $this->eventHistory[] = [
            'event' => $event,
            'data' => $data,
            'timestamp' => now(),
            'observers_count' => $this->observers->count()
        ];

        // Keep only last 100 events
        if (count($this->eventHistory) > 100) {
            array_shift($this->eventHistory);
        }

        Log::info("ChatEventManager: Notifying observers of event: {$event}", [
            'event' => $event,
            'data_keys' => array_keys($data),
            'observers_count' => $this->observers->count()
        ]);

        // Notify all observers
        foreach ($this->observers as $observer) {
            try {
                $observer->update($event, $data);
            } catch (\Exception $e) {
                Log::error('Error in observer notification', [
                    'observer_class' => get_class($observer),
                    'event' => $event,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
    }

    /**
     * Emit a message sent event
     */
    public function emitMessageSent($message, $conversation): void
    {
        $this->notify('message_sent', [
            'message' => $message,
            'conversation' => $conversation,
            'timestamp' => now()
        ]);
    }

    /**
     * Emit a conversation status changed event
     */
    public function emitConversationStatusChanged($conversation, string $oldStatus, string $newStatus): void
    {
        $this->notify('conversation_status_changed', [
            'conversation' => $conversation,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'timestamp' => now()
        ]);
    }

    /**
     * Emit a conversation terminated event
     */
    public function emitConversationTerminated($conversation, string $terminatedBy, ?string $reason = null): void
    {
        $this->notify('conversation_terminated', [
            'conversation' => $conversation,
            'terminated_by' => $terminatedBy,
            'reason' => $reason,
            'timestamp' => now()
        ]);
    }

    /**
     * Emit a conversation activated event
     */
    public function emitConversationActivated($conversation, $agent): void
    {
        $this->notify('conversation_activated', [
            'conversation' => $conversation,
            'agent' => $agent,
            'timestamp' => now()
        ]);
    }

    /**
     * Emit a queue position changed event
     */
    public function emitQueuePositionChanged(int $conversationId, int $newPosition, int $estimatedWait): void
    {
        $this->notify('queue_position_changed', [
            'conversation_id' => $conversationId,
            'position' => $newPosition,
            'estimated_wait' => $estimatedWait,
            'timestamp' => now()
        ]);
    }

    /**
     * Emit an agent status changed event
     */
    public function emitAgentStatusChanged(int $agentId, string $oldStatus, string $newStatus): void
    {
        $this->notify('agent_status_changed', [
            'agent_id' => $agentId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'timestamp' => now()
        ]);
    }

    /**
     * Get all attached observers
     */
    public function getObservers(): SplObjectStorage
    {
        return $this->observers;
    }

    /**
     * Get event history for debugging
     */
    public function getEventHistory(): array
    {
        return $this->eventHistory;
    }

    /**
     * Get statistics
     */
    public function getStats(): array
    {
        return [
            'observers_count' => $this->observers->count(),
            'events_processed' => count($this->eventHistory),
            'last_event' => end($this->eventHistory) ?: null
        ];
    }

    /**
     * Clear event history
     */
    public function clearEventHistory(): void
    {
        $this->eventHistory = [];
        Log::info('ChatEventManager: Event history cleared');
    }
}
