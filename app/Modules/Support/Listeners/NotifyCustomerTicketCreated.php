<?php

namespace App\Modules\Support\Listeners;

use App\Modules\Support\Events\TicketCreated;
use App\Modules\Support\Notifications\TicketCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class NotifyCustomerTicketCreated implements ShouldQueue
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
    public function handle(TicketCreated $event): void
    {
        try {
            // Send email notification to customer
            $event->ticket->user->notify(new TicketCreatedNotification($event->ticket));

            Log::info('Ticket created notification sent', [
                'ticket_id' => $event->ticket->id,
                'ticket_number' => $event->ticket->ticket_number,
                'customer_id' => $event->ticket->user_id,
                'customer_email' => $event->ticket->user->email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send ticket created notification', [
                'ticket_id' => $event->ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
