<?php

namespace App\Modules\Support\Listeners;

use App\Modules\Support\Events\TicketCreated;
use App\Modules\Support\Notifications\NewTicketNotification;
use App\Modules\User\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class NotifyAdminsNewTicket implements ShouldQueue
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
            // Get all admin users
            $admins = User::where('is_admin', true)->get();

            foreach ($admins as $admin) {
                $admin->notify(new NewTicketNotification($event->ticket));
            }

            Log::info('New ticket notification sent to admins', [
                'ticket_id' => $event->ticket->id,
                'ticket_number' => $event->ticket->ticket_number,
                'admin_count' => $admins->count(),
                'priority' => $event->ticket->priority
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send new ticket notification to admins', [
                'ticket_id' => $event->ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

