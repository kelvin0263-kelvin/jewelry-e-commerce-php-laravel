<?php

namespace App\Modules\Support\Listeners;

use App\Modules\Support\Events\TicketAssigned;
use App\Modules\Support\Notifications\TicketAssignedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendTicketAssignmentNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TicketAssigned $event): void
    {
        try {
            // Notify the assigned agent
            $event->agent->notify(new TicketAssignedNotification($event->ticket));

            Log::info('Ticket assignment notification sent', [
                'ticket_id' => $event->ticket->id,
                'ticket_number' => $event->ticket->ticket_number,
                'agent_id' => $event->agent->id,
                'agent_email' => $event->agent->email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send ticket assignment notification', [
                'ticket_id' => $event->ticket->id,
                'agent_id' => $event->agent->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
