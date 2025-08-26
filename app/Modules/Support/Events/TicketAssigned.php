<?php

namespace App\Modules\Support\Events;

use App\Modules\Support\Models\Ticket;
use App\Modules\User\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Ticket $ticket;
    public User $agent;
    public ?User $previousAgent;

    /**
     * Create a new event instance.
     */
    public function __construct(Ticket $ticket, User $agent, ?User $previousAgent = null)
    {
        $this->ticket = $ticket;
        $this->agent = $agent;
        $this->previousAgent = $previousAgent;
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

