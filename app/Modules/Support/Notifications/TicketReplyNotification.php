<?php

namespace App\Modules\Support\Notifications;

use App\Modules\Support\Models\Ticket;
use App\Modules\Support\Models\TicketReply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Ticket $ticket;
    public TicketReply $reply;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, TicketReply $reply)
    {
        $this->ticket = $ticket;
        $this->reply = $reply;
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
        $isFromAgent = $this->reply->reply_type === 'agent';
        $replyFrom = $isFromAgent ? 'Support Agent' : 'Customer';
        $icon = $isFromAgent ? '💬' : '📞';
        
        return (new MailMessage)
            ->subject("{$icon} New Reply - Ticket #{$this->ticket->ticket_number}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new reply has been added to your support ticket.")
            ->line("**Ticket Number:** {$this->ticket->ticket_number}")
            ->line("**Subject:** {$this->ticket->subject}")
            ->line("**Reply From:** {$replyFrom} ({$this->reply->user->name})")
            ->line("**Replied At:** {$this->reply->created_at->format('M j, Y g:i A')}")
            ->line("**Message:**")
            ->line('"' . substr($this->reply->message, 0, 200) . (strlen($this->reply->message) > 200 ? '...' : '') . '"')
            ->when($this->reply->is_first_response, function ($mail) {
                return $mail->line("✅ **This is the first response to your ticket.**");
            })
            ->action('View Full Conversation', url("/support/tickets/{$this->ticket->id}"))
            ->line($isFromAgent ? 'You can reply to continue the conversation.' : 'Please review and respond if needed.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'ticket_reply',
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'reply_id' => $this->reply->id,
            'reply_type' => $this->reply->reply_type,
            'reply_from' => $this->reply->user->name,
            'subject' => $this->ticket->subject,
            'is_first_response' => $this->reply->is_first_response,
            'message_preview' => substr($this->reply->message, 0, 100) . (strlen($this->reply->message) > 100 ? '...' : ''),
            'message' => "New reply on ticket #{$this->ticket->ticket_number}",
            'action_url' => url("/support/tickets/{$this->ticket->id}")
        ];
    }
}

