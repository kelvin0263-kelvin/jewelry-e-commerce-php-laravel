<?php

namespace App\Modules\Support\Notifications;

use App\Modules\Support\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketAssignedNotification extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject("📋 Ticket Assigned - #{$this->ticket->ticket_number}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A support ticket has been assigned to you.")
            ->line("**Ticket Number:** {$this->ticket->ticket_number}")
            ->line("**Customer:** {$this->ticket->user->name} ({$this->ticket->user->email})")
            ->line("**Subject:** {$this->ticket->subject}")
            ->line("**Priority:** " . ucfirst($this->ticket->priority))
            ->line("**Category:** {$this->ticket->category_display}")
            ->line("**Created:** {$this->ticket->created_at->format('M j, Y g:i A')}")
            ->when($this->ticket->priority === 'urgent', function ($mail) {
                return $mail->line("⚠️ **This is an URGENT ticket that requires immediate attention!**");
            })
            ->when($this->ticket->is_overdue, function ($mail) {
                return $mail->line("🕐 **This ticket is overdue and needs immediate response!**");
            })
            ->action('View Ticket', url("/admin/support/tickets/{$this->ticket->id}"))
            ->line('Please review the ticket details and respond as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'ticket_assigned',
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'customer_name' => $this->ticket->user->name,
            'customer_email' => $this->ticket->user->email,
            'subject' => $this->ticket->subject,
            'priority' => $this->ticket->priority,
            'category' => $this->ticket->category,
            'is_urgent' => $this->ticket->priority === 'urgent',
            'is_overdue' => $this->ticket->is_overdue,
            'message' => "Ticket #{$this->ticket->ticket_number} has been assigned to you",
            'action_url' => url("/admin/support/tickets/{$this->ticket->id}")
        ];
    }
}

