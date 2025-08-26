<?php

namespace App\Modules\Support\Events;

use App\Modules\Support\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationTerminated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversation;
    public $terminatedBy;
    public $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(Conversation $conversation, $terminatedBy, $reason = null)
    {
        $this->conversation = $conversation;
        $this->terminatedBy = $terminatedBy; // 'admin' or 'customer'
        $this->reason = $reason;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.' . $this->conversation->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'ConversationTerminated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversation->id,
            'terminated_by' => $this->terminatedBy,
            'reason' => $this->reason,
            'status' => $this->conversation->status,
            'ended_at' => $this->conversation->ended_at?->toISOString(),
        ];
    }
}
