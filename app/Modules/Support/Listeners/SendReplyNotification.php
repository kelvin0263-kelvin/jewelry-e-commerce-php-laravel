<?php

namespace App\Modules\Support\Listeners;

use App\Modules\Support\Events\TicketReplyAdded;
use App\Modules\Support\Notifications\TicketReplyNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendReplyNotification implements ShouldQueue
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
    public function handle(TicketReplyAdded $event): void
    {
        try {
            // Don't send notifications for internal notes
            if ($event->reply->is_internal) {
                return;
            }

            // If it's from an agent, notify the customer
            if ($event->reply->reply_type === 'agent') {
                $event->ticket->user->notify(new TicketReplyNotification($event->ticket, $event->reply));
                
                Log::info('Ticket reply notification sent to customer', [
                    'ticket_id' => $event->ticket->id,
                    'reply_id' => $event->reply->id,
                    'customer_id' => $event->ticket->user_id,
                    'customer_email' => $event->ticket->user->email
                ]);
            } 
            // If it's from a customer, notify the assigned agent
            elseif ($event->reply->reply_type === 'customer' && $event->ticket->assignedAgent) {
                $event->ticket->assignedAgent->notify(new TicketReplyNotification($event->ticket, $event->reply));
                
                Log::info('Ticket reply notification sent to agent', [
                    'ticket_id' => $event->ticket->id,
                    'reply_id' => $event->reply->id,
                    'agent_id' => $event->ticket->assigned_agent_id,
                    'agent_email' => $event->ticket->assignedAgent->email
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send ticket reply notification', [
                'ticket_id' => $event->ticket->id,
                'reply_id' => $event->reply->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
