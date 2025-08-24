<?php

namespace App\Services;

use App\Models\ChatQueue;
use App\Models\AgentStatus;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ChatQueueService
{
    /**
     * Add customer to chat queue
     */
    public function addToQueue(
        int $conversationId, 
        int $customerId, 
        string $priority = 'normal',
        array $context = null,
        string $initialMessage = null
    ): ChatQueue {
        return DB::transaction(function () use ($conversationId, $customerId, $priority, $context, $initialMessage) {
            // Check if already in queue
            $existingQueue = ChatQueue::where('conversation_id', $conversationId)
                ->where('status', 'waiting')
                ->first();

            if ($existingQueue) {
                return $existingQueue;
            }

            // Add to queue
            $queueItem = ChatQueue::addToQueue($conversationId, $customerId, $priority, $context, $initialMessage);

            Log::info('Customer added to chat queue', [
                'customer_id' => $customerId,
                'conversation_id' => $conversationId,
                'position' => $queueItem->position,
                'priority' => $priority
            ]);

            // Note: Auto-assignment disabled for manual queue management
            // Agents must manually accept chats from the queue
            // $this->tryAutoAssignment();

            return $queueItem;
        });
    }

    /**
     * Try to automatically assign waiting chats to available agents
     */
    public function tryAutoAssignment(): void
    {
        $availableAgents = AgentStatus::getAvailableAgents();
        
        if ($availableAgents->isEmpty()) {
            return;
        }

        // Get waiting chats in FIFO order with priority
        $waitingChats = ChatQueue::waiting()
            ->byPriority()
            ->fifoOrder()
            ->limit($availableAgents->count())
            ->get();

        foreach ($waitingChats as $chat) {
            $agent = $this->selectBestAgent($availableAgents);
            
            if ($agent) {
                $this->assignChatToAgent($chat, $agent->user_id);
                
                // Remove assigned agent from available list
                $availableAgents = $availableAgents->reject(function ($item) use ($agent) {
                    return $item->user_id === $agent->user_id;
                });

                if ($availableAgents->isEmpty()) {
                    break;
                }
            }
        }
    }

    /**
     * Manual assignment of chat to specific agent
     */
    public function assignChatToAgent(ChatQueue $queueItem, int $agentId): bool
    {
        return DB::transaction(function () use ($queueItem, $agentId) {
            $agentStatus = AgentStatus::where('user_id', $agentId)->first();
            
            if (!$agentStatus || !$agentStatus->canAcceptChats()) {
                Log::warning('Cannot assign chat to agent', [
                    'agent_id' => $agentId,
                    'queue_item_id' => $queueItem->id,
                    'reason' => 'Agent not available'
                ]);
                return false;
            }

            $queueItem->assignToAgent($agentId);
            $agentStatus->checkAndUpdateStatus();

            Log::info('Chat assigned to agent', [
                'agent_id' => $agentId,
                'customer_id' => $queueItem->customer_id,
                'wait_time' => $queueItem->wait_time_seconds
            ]);

            return true;
        });
    }

    /**
     * Select best available agent based on workload and skills
     */
    private function selectBestAgent($availableAgents): ?AgentStatus
    {
        if ($availableAgents->isEmpty()) {
            return null;
        }

        // For now, select agent with lowest workload
        // Can be enhanced with skill matching, performance metrics, etc.
        return $availableAgents->sortBy(function ($agent) {
            return $agent->getWorkloadPercentage();
        })->first();
    }

    /**
     * Get queue status for customer
     */
    public function getQueueStatus(int $conversationId): array
    {
        $queueItem = ChatQueue::where('conversation_id', $conversationId)
            ->where('status', 'waiting')
            ->first();

        if (!$queueItem) {
            return [
                'in_queue' => false,
                'position' => 0,
                'estimated_wait' => 0
            ];
        }

        $position = $queueItem->position_in_queue;
        $estimatedWait = ChatQueue::getEstimatedWaitTime($position);

        return [
            'in_queue' => true,
            'position' => $position,
            'estimated_wait' => $estimatedWait,
            'wait_time' => $queueItem->wait_time,
            'queued_at' => $queueItem->queued_at
        ];
    }

    /**
     * Get pending chats for admin interface
     */
    public function getPendingChats(): \Illuminate\Database\Eloquent\Collection
    {
        return ChatQueue::with(['customer', 'conversation'])
            ->waiting()
            ->byPriority()
            ->fifoOrder()
            ->get();
    }

    /**
     * Get active chats for agent
     */
    public function getActiveChatsForAgent(int $agentId): \Illuminate\Database\Eloquent\Collection
    {
        return ChatQueue::with(['customer', 'conversation.messages'])
            ->assigned()
            ->where('assigned_agent_id', $agentId)
            ->get();
    }

    /**
     * Complete a chat session
     */
    public function completeChat(int $conversationId, string $reason = 'resolved', array $feedback = null): bool
    {
        return DB::transaction(function () use ($conversationId, $reason, $feedback) {
            $queueItem = ChatQueue::where('conversation_id', $conversationId)
                ->whereIn('status', ['assigned', 'waiting'])
                ->first();

            if (!$queueItem) {
                return false;
            }

            $queueItem->complete($reason);

            // Update conversation with feedback if provided
            if ($feedback) {
                $queueItem->conversation->update([
                    'customer_rating' => $feedback['rating'] ?? null,
                    'customer_feedback' => $feedback['feedback'] ?? null
                ]);
            }

            // Update agent status
            if ($queueItem->assigned_agent_id) {
                $agentStatus = AgentStatus::where('user_id', $queueItem->assigned_agent_id)->first();
                $agentStatus?->checkAndUpdateStatus();
            }

            // Note: Auto-assignment disabled for manual queue management
            // $this->tryAutoAssignment();

            Log::info('Chat completed', [
                'conversation_id' => $conversationId,
                'reason' => $reason,
                'agent_id' => $queueItem->assigned_agent_id
            ]);

            return true;
        });
    }

    /**
     * Abandon chat (customer left)
     */
    public function abandonChat(int $conversationId): bool
    {
        return $this->completeChat($conversationId, 'abandoned');
    }

    /**
     * Transfer chat to another agent
     */
    public function transferChat(int $conversationId, int $newAgentId, string $reason = null): bool
    {
        return DB::transaction(function () use ($conversationId, $newAgentId, $reason) {
            $queueItem = ChatQueue::where('conversation_id', $conversationId)
                ->where('status', 'assigned')
                ->first();

            if (!$queueItem) {
                return false;
            }

            $oldAgentId = $queueItem->assigned_agent_id;

            // Check if new agent can accept chat
            $newAgentStatus = AgentStatus::where('user_id', $newAgentId)->first();
            if (!$newAgentStatus || !$newAgentStatus->canAcceptChats()) {
                return false;
            }

            // Update queue item
            $queueItem->update([
                'assigned_agent_id' => $newAgentId,
                'assigned_at' => now()
            ]);

            // Update conversation
            $queueItem->conversation->update([
                'assigned_agent_id' => $newAgentId
            ]);

            // Update agent statuses
            if ($oldAgentId) {
                $oldAgentStatus = AgentStatus::where('user_id', $oldAgentId)->first();
                $oldAgentStatus?->decrement('current_active_chats');
                $oldAgentStatus?->checkAndUpdateStatus();
            }

            $newAgentStatus->increment('current_active_chats');
            $newAgentStatus->checkAndUpdateStatus();

            Log::info('Chat transferred', [
                'conversation_id' => $conversationId,
                'from_agent' => $oldAgentId,
                'to_agent' => $newAgentId,
                'reason' => $reason
            ]);

            return true;
        });
    }

    /**
     * Get queue statistics
     */
    public function getQueueStats(): array
    {
        return [
            'waiting_customers' => ChatQueue::waiting()->count(),
            'active_chats' => ChatQueue::assigned()->count(),
            'available_agents' => AgentStatus::available()->count(),
            'online_agents' => AgentStatus::online()->count(),
            'total_capacity' => AgentStatus::getTotalQueueCapacity(),
            'average_wait_time' => ChatQueue::where('assigned_at', '>=', now()->subHour())
                ->avg('wait_time_seconds') ?? 0,
            'longest_wait' => ChatQueue::waiting()->max('queued_at') 
                ? now()->diffInMinutes(ChatQueue::waiting()->min('queued_at'))
                : 0
        ];
    }

    /**
     * Clean up old completed queue items
     */
    public function cleanupOldQueueItems(int $daysOld = 30): int
    {
        return ChatQueue::whereIn('status', ['completed', 'abandoned'])
            ->where('completed_at', '<', now()->subDays($daysOld))
            ->delete();
    }
}