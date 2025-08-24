<?php

namespace App\Modules\Support\Events;

use App\Modules\Support\Models\Ticket;
use App\Modules\User\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketResolved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Ticket $ticket;
    public ?User $resolvedBy;
    public int $resolutionTimeHours;

    /**
     * Create a new event instance.
     */
    public function __construct(Ticket $ticket, ?User $resolvedBy = null, int $resolutionTimeHours = 0)
    {
        $this->ticket = $ticket;
        $this->resolvedBy = $resolvedBy;
        $this->resolutionTimeHours = $resolutionTimeHours;
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
