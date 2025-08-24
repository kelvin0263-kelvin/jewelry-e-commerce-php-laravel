<?php

namespace App\Modules\Support\Notifications;

use App\Modules\Support\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTicketNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Ticket $ticket;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $priorityColor = $this->ticket->priority === 'urgent' ? 'red' : ($this->ticket->priority === 'high' ? 'orange' : 'blue');
        
        return (new MailMessage)
            ->subject("🎫 New Support Ticket - #{$this->ticket->ticket_number} ({$this->ticket->priority})")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new support ticket has been created and requires attention.")
            ->line("**Ticket Number:** {$this->ticket->ticket_number}")
            ->line("**Customer:** {$this->ticket->user->name} ({$this->ticket->user->email})")
            ->line("**Subject:** {$this->ticket->subject}")
            ->line("**Priority:** " . ucfirst($this->ticket->priority))
            ->line("**Category:** {$this->ticket->category_display}")
            ->line("**Created:** {$this->ticket->created_at->format('M j, Y g:i A')}")
            ->when($this->ticket->priority === 'urgent', function ($mail) {
                return $mail->line("⚠️ **This is an URGENT ticket that requires immediate attention!**");
            })
            ->action('View Ticket', url("/admin/support/tickets/{$this->ticket->id}"))
            ->line('Please review and assign this ticket to an appropriate agent.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_ticket',
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'customer_name' => $this->ticket->user->name,
            'customer_email' => $this->ticket->user->email,
            'subject' => $this->ticket->subject,
            'priority' => $this->ticket->priority,
            'category' => $this->ticket->category,
            'is_urgent' => $this->ticket->priority === 'urgent',
            'message' => "New support ticket #{$this->ticket->ticket_number} from {$this->ticket->user->name}",
            'action_url' => url("/admin/support/tickets/{$this->ticket->id}")
        ];
    }
}
