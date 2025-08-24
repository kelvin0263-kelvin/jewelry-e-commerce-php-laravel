<?php

namespace App\Modules\Support\Models;

use App\Modules\User\Models\User;
use App\Modules\Support\Events\TicketCreated;
use App\Modules\Support\Events\TicketStatusChanged;
use App\Modules\Support\Events\TicketAssigned;
use App\Modules\Support\Events\TicketEscalated;
use App\Modules\Support\Events\TicketResolved;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'assigned_agent_id',
        'subject',
        'description',
        'priority',
        'category',
        'status',
        'contact_email',
        'contact_phone',
        'preferred_contact_method',
        'first_response_at',
        'resolved_at',
        'closed_at',
        'response_time_hours',
        'resolution_time_hours',
        'satisfaction_rating',
        'customer_feedback',
        'metadata',
        'is_escalated',
        'escalated_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_escalated' => 'boolean',
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'escalated_at' => 'datetime',
        'satisfaction_rating' => 'integer'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class);
    }

    public function publicReplies(): HasMany
    {
        return $this->hasMany(TicketReply::class)->where('is_internal', false);
    }

    public function internalNotes(): HasMany
    {
        return $this->hasMany(TicketReply::class)->where('is_internal', true);
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress', 'waiting_customer', 'waiting_agent']);
    }

    public function scopeClosed($query)
    {
        return $query->whereIn('status', ['resolved', 'closed']);
    }

    public function scopeByPriority($query)
    {
        return $query->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')");
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_agent_id');
    }

    public function scopeAssignedTo($query, $agentId)
    {
        return $query->where('assigned_agent_id', $agentId);
    }

    // Mutators & Accessors
    public function getPriorityBadgeClassAttribute()
    {
        return match ($this->priority) {
            'urgent' => 'badge-danger',
            'high' => 'badge-warning',
            'normal' => 'badge-info',
            'low' => 'badge-secondary',
            default => 'badge-secondary'
        };
    }

    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'open' => 'badge-primary',
            'in_progress' => 'badge-warning',
            'waiting_customer' => 'badge-info',
            'waiting_agent' => 'badge-secondary',
            'resolved' => 'badge-success',
            'closed' => 'badge-dark',
            default => 'badge-secondary'
        };
    }

    public function getCategoryDisplayAttribute()
    {
        return match ($this->category) {
            'general_inquiry' => 'General Inquiry',
            'product_issue' => 'Product Issue',
            'order_problem' => 'Order Problem',
            'billing_question' => 'Billing Question',
            'account_help' => 'Account Help',
            'technical_support' => 'Technical Support',
            'return_refund' => 'Return/Refund',
            'shipping_delivery' => 'Shipping/Delivery',
            'other' => 'Other',
            default => 'General Inquiry'
        };
    }

    public function getIsOverdueAttribute()
    {
        if ($this->status === 'closed' || $this->status === 'resolved') {
            return false;
        }

        $slaHours = match ($this->priority) {
            'urgent' => 2,
            'high' => 8,
            'normal' => 24,
            'low' => 72,
            default => 24
        };

        return $this->created_at->addHours($slaHours)->isPast();
    }

    public function getTimeToResponseAttribute()
    {
        if (!$this->first_response_at) {
            return $this->created_at->diffForHumans();
        }
        return $this->created_at->diffForHumans($this->first_response_at);
    }

    // Static methods
    public static function generateTicketNumber(): string
    {
        $year = date('Y');
        $lastTicket = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastTicket ? 
            intval(substr($lastTicket->ticket_number, -6)) + 1 : 1;

        return sprintf('TKT-%s-%06d', $year, $nextNumber);
    }

    public static function getCategories(): array
    {
        return [
            'general_inquiry' => 'General Inquiry',
            'product_issue' => 'Product Issue',
            'order_problem' => 'Order Problem',
            'billing_question' => 'Billing Question',
            'account_help' => 'Account Help',
            'technical_support' => 'Technical Support',
            'return_refund' => 'Return/Refund',
            'shipping_delivery' => 'Shipping/Delivery',
            'other' => 'Other'
        ];
    }

    public static function getPriorities(): array
    {
        return [
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent'
        ];
    }

    // Instance methods
    public function assignToAgent($agentId): void
    {
        $previousAgent = $this->assignedAgent;
        $oldStatus = $this->status;
        $newStatus = $this->status === 'open' ? 'in_progress' : $this->status;
        
        $this->update([
            'assigned_agent_id' => $agentId,
            'status' => $newStatus
        ]);

        // Fire events
        if ($oldStatus !== $newStatus) {
            TicketStatusChanged::dispatch($this, $oldStatus, $newStatus);
        }
        
        TicketAssigned::dispatch($this, $this->fresh()->assignedAgent, $previousAgent);
    }

    public function markAsResolved($agentId = null): void
    {
        $oldStatus = $this->status;
        $resolutionTimeHours = $this->created_at->diffInHours(now());
        $resolvedBy = $agentId ? User::find($agentId) : null;
        
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolution_time_hours' => $resolutionTimeHours
        ]);

        // Fire events
        TicketStatusChanged::dispatch($this, $oldStatus, 'resolved');
        TicketResolved::dispatch($this, $resolvedBy, $resolutionTimeHours);
    }

    public function close($agentId = null): void
    {
        $oldStatus = $this->status;
        
        $this->update([
            'status' => 'closed',
            'closed_at' => now()
        ]);

        // Fire event
        if ($oldStatus !== 'closed') {
            TicketStatusChanged::dispatch($this, $oldStatus, 'closed');
        }
    }

    public function reopen(): void
    {
        $oldStatus = $this->status;
        
        $this->update([
            'status' => 'open',
            'resolved_at' => null,
            'closed_at' => null
        ]);

        // Fire event
        TicketStatusChanged::dispatch($this, $oldStatus, 'open');
    }

    public function escalate(string $reason = 'Manual escalation'): void
    {
        $oldPriority = $this->priority;
        $newPriority = match($this->priority) {
            'low' => 'normal',
            'normal' => 'high',
            'high' => 'urgent',
            'urgent' => 'urgent', // Already at highest priority
            default => 'normal'
        };
        
        $this->update([
            'is_escalated' => true,
            'escalated_at' => now(),
            'priority' => $newPriority
        ]);

        // Fire event
        TicketEscalated::dispatch($this, $reason, $oldPriority, $newPriority);
    }

    public function recordFirstResponse(): void
    {
        if (!$this->first_response_at) {
            $this->update([
                'first_response_at' => now(),
                'response_time_hours' => $this->created_at->diffInHours(now()),
                'status' => 'waiting_customer'
            ]);
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (!$ticket->ticket_number) {
                $ticket->ticket_number = self::generateTicketNumber();
            }
        });

        static::created(function ($ticket) {
            // Fire TicketCreated event
            TicketCreated::dispatch($ticket);
        });

        static::updating(function ($ticket) {
            // Track status changes
            if ($ticket->isDirty('status')) {
                $original = $ticket->getOriginal('status');
                $new = $ticket->status;
                
                // We'll dispatch this after the update is complete
                $ticket->_statusChanged = ['old' => $original, 'new' => $new];
            }
        });

        static::updated(function ($ticket) {
            // Fire TicketStatusChanged event if status was changed
            if (isset($ticket->_statusChanged)) {
                TicketStatusChanged::dispatch(
                    $ticket,
                    $ticket->_statusChanged['old'],
                    $ticket->_statusChanged['new']
                );
                unset($ticket->_statusChanged);
            }
        });
    }
}