<?php

namespace App\Modules\Support\Listeners;

use App\Modules\Support\Events\TicketCreated;
use App\Modules\Support\Events\TicketStatusChanged;
use App\Modules\Support\Events\TicketAssigned;
use App\Modules\Support\Events\TicketReplyAdded;
use App\Modules\Support\Events\TicketEscalated;
use App\Modules\Support\Events\TicketResolved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogTicketActivity implements ShouldQueue
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
     * Handle the TicketCreated event.
     */
    public function handleTicketCreated(TicketCreated $event): void
    {
        Log::channel('support')->info('Ticket created', [
            'event' => 'ticket.created',
            'ticket_id' => $event->ticket->id,
            'ticket_number' => $event->ticket->ticket_number,
            'customer_id' => $event->ticket->user_id,
            'customer_email' => $event->ticket->user->email,
            'subject' => $event->ticket->subject,
            'priority' => $event->ticket->priority,
            'category' => $event->ticket->category,
            'created_at' => $event->ticket->created_at->toISOString()
        ]);
    }

    /**
     * Handle the TicketStatusChanged event.
     */
    public function handleTicketStatusChanged(TicketStatusChanged $event): void
    {
        Log::channel('support')->info('Ticket status changed', [
            'event' => 'ticket.status_changed',
            'ticket_id' => $event->ticket->id,
            'ticket_number' => $event->ticket->ticket_number,
            'old_status' => $event->oldStatus,
            'new_status' => $event->newStatus,
            'changed_at' => now()->toISOString()
        ]);
    }

    /**
     * Handle the TicketAssigned event.
     */
    public function handleTicketAssigned(TicketAssigned $event): void
    {
        Log::channel('support')->info('Ticket assigned', [
            'event' => 'ticket.assigned',
            'ticket_id' => $event->ticket->id,
            'ticket_number' => $event->ticket->ticket_number,
            'agent_id' => $event->agent->id,
            'agent_name' => $event->agent->name,
            'agent_email' => $event->agent->email,
            'previous_agent_id' => $event->previousAgent?->id,
            'previous_agent_name' => $event->previousAgent?->name,
            'assigned_at' => now()->toISOString()
        ]);
    }

    /**
     * Handle the TicketReplyAdded event.
     */
    public function handleTicketReplyAdded(TicketReplyAdded $event): void
    {
        Log::channel('support')->info('Ticket reply added', [
            'event' => 'ticket.reply_added',
            'ticket_id' => $event->ticket->id,
            'ticket_number' => $event->ticket->ticket_number,
            'reply_id' => $event->reply->id,
            'reply_type' => $event->reply->reply_type,
            'is_internal' => $event->reply->is_internal,
            'is_first_response' => $event->reply->is_first_response,
            'author_id' => $event->reply->user_id,
            'author_name' => $event->reply->user->name,
            'created_at' => $event->reply->created_at->toISOString()
        ]);
    }

    /**
     * Handle the TicketEscalated event.
     */
    public function handleTicketEscalated(TicketEscalated $event): void
    {
        Log::channel('support')->warning('Ticket escalated', [
            'event' => 'ticket.escalated',
            'ticket_id' => $event->ticket->id,
            'ticket_number' => $event->ticket->ticket_number,
            'reason' => $event->reason,
            'old_priority' => $event->oldPriority,
            'new_priority' => $event->newPriority,
            'escalated_at' => $event->ticket->escalated_at->toISOString()
        ]);
    }

    /**
     * Handle the TicketResolved event.
     */
    public function handleTicketResolved(TicketResolved $event): void
    {
        Log::channel('support')->info('Ticket resolved', [
            'event' => 'ticket.resolved',
            'ticket_id' => $event->ticket->id,
            'ticket_number' => $event->ticket->ticket_number,
            'resolved_by_id' => $event->resolvedBy?->id,
            'resolved_by_name' => $event->resolvedBy?->name,
            'resolution_time_hours' => $event->resolutionTimeHours,
            'created_at' => $event->ticket->created_at->toISOString(),
            'resolved_at' => $event->ticket->resolved_at->toISOString()
        ]);
    }
}
