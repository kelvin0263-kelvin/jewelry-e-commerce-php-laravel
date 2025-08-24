<?php

namespace App\Modules\Support\Events;

use App\Modules\Support\Models\Ticket;
use App\Modules\Support\Models\TicketReply;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketReplyAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Ticket $ticket;
    public TicketReply $reply;

    /**
     * Create a new event instance.
     */
    public function __construct(Ticket $ticket, TicketReply $reply)
    {
        $this->ticket = $ticket;
        $this->reply = $reply;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
