<?php

namespace App\Modules\Support\Models;

use App\Modules\User\Models\User;
use App\Modules\Support\Events\TicketReplyAdded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'reply_type',
        'attachments',
        'is_internal',
        'is_first_response',
        'read_at'
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_internal' => 'boolean',
        'is_first_response' => 'boolean',
        'read_at' => 'datetime'
    ];

    // Relationships
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_internal', false);
    }

    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    public function scopeFromCustomer($query)
    {
        return $query->where('reply_type', 'customer');
    }

    public function scopeFromAgent($query)
    {
        return $query->where('reply_type', 'agent');
    }

    public function scopeSystem($query)
    {
        return $query->where('reply_type', 'system');
    }

    // Accessors
    public function getIsFromCustomerAttribute()
    {
        return $this->reply_type === 'customer';
    }

    public function getIsFromAgentAttribute()
    {
        return $this->reply_type === 'agent';
    }

    public function getIsSystemMessageAttribute()
    {
        return $this->reply_type === 'system';
    }

    public function getUserTypeDisplayAttribute()
    {
        return match ($this->reply_type) {
            'customer' => 'Customer',
            'agent' => 'Support Agent',
            'system' => 'System',
            default => 'Unknown'
        };
    }

    // Instance methods
    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($reply) {
            // Update ticket status based on reply type
            $ticket = $reply->ticket;
            
            if ($reply->reply_type === 'agent' && !$reply->is_internal) {
                // Agent replied to customer
                $ticket->update(['status' => 'waiting_customer']);
                
                // Mark as first response if it's the first agent reply
                if (!$ticket->first_response_at) {
                    $reply->update(['is_first_response' => true]);
                    $ticket->recordFirstResponse();
                }
            } elseif ($reply->reply_type === 'customer') {
                // Customer replied
                $ticket->update(['status' => 'waiting_agent']);
            }

            // Fire TicketReplyAdded event
            TicketReplyAdded::dispatch($ticket, $reply);
        });
    }
}