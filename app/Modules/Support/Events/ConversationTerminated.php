<?php

namespace App\Modules\Support\Events;

use App\Modules\Support\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationTerminated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversation;
    public $terminatedBy;
    public $reason;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Conversation $conversation, string $terminatedBy, ?string $reason = null)
    {
        $this->conversation = $conversation;
        $this->terminatedBy = $terminatedBy; // 'admin' or 'customer'  
        $this->reason = $reason;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('conversation.' . $this->conversation->id);
    }
    
    /**
     * Data to broadcast with the event
     */
    public function broadcastWith()
    {
        return [
            'conversation_id' => $this->conversation->id,
            'terminatedBy' => $this->terminatedBy,
            'reason' => $this->reason,
            'timestamp' => now()->toISOString()
        ];
    }
}
