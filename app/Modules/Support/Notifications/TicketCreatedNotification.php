<?php

namespace App\Modules\Support\Notifications;

use App\Modules\Support\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification implements ShouldQueue
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
            ->subject("Support Ticket Created - #{$this->ticket->ticket_number}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your support ticket has been successfully created.")
            ->line("**Ticket Number:** {$this->ticket->ticket_number}")
            ->line("**Subject:** {$this->ticket->subject}")
            ->line("**Priority:** " . ucfirst($this->ticket->priority))
            ->line("**Category:** {$this->ticket->category_display}")
            ->line("We'll review your ticket and get back to you as soon as possible.")
            ->action('View Ticket', url("/support/tickets/{$this->ticket->id}"))
            ->line('Thank you for contacting our support team!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'ticket_created',
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'subject' => $this->ticket->subject,
            'priority' => $this->ticket->priority,
            'category' => $this->ticket->category,
            'message' => "Your support ticket #{$this->ticket->ticket_number} has been created.",
            'action_url' => url("/support/tickets/{$this->ticket->id}")
        ];
    }
}

