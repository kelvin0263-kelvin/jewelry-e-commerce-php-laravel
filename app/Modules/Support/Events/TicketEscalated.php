<?php

namespace App\Modules\Support\Events;

use App\Modules\Support\Models\Ticket;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketEscalated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Ticket $ticket;
    public string $reason;
    public string $oldPriority;
    public string $newPriority;

    /**
     * Create a new event instance.
     */
    public function __construct(Ticket $ticket, string $reason, string $oldPriority, string $newPriority)
    {
        $this->ticket = $ticket;
        $this->reason = $reason;
        $this->oldPriority = $oldPriority;
        $this->newPriority = $newPriority;
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
