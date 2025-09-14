<?php
/**
 * Author: TAN CHUN KEAT
 * Date: 2025-09-15
 */
namespace App\Modules\Support\Events;

use App\Modules\Support\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; 
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The message instance.
     */
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.

     */
    public function broadcastOn(): array
    {
        // Broadcast on a private channel for the specific conversation
        return [
            new PrivateChannel('conversation.'.$this->message->conversation_id),
        ];
    }

    /**
     * Get the broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'MessageSent';
    }
}