<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatQueue extends Model
{
    use HasFactory;

    protected $table = 'chat_queue';

    protected $fillable = [
        'conversation_id',
        'customer_id',
        'status',
        'position',
        'assigned_agent_id',
        'queued_at',
        'assigned_at',
        'completed_at',
        'wait_time_seconds',
        'priority',
        'escalation_context',
        'initial_message'
    ];

    protected $casts = [
        'queued_at' => 'datetime',
        'assigned_at' => 'datetime', 
        'completed_at' => 'datetime',
        'escalation_context' => 'array'
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    // Scopes
    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeByPriority($query)
    {
        return $query->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')");
    }

    public function scopeFifoOrder($query)
    {
        return $query->orderBy('position')->orderBy('queued_at');
    }

    // Static methods for queue management
    public static function addToQueue($conversationId, $customerId, $priority = 'normal', $context = null, $initialMessage = null)
    {
        $position = self::getNextPosition();
        
        return self::create([
            'conversation_id' => $conversationId,
            'customer_id' => $customerId,
            'status' => 'waiting',
            'position' => $position,
            'queued_at' => now(),
            'priority' => $priority,
            'escalation_context' => $context,
            'initial_message' => $initialMessage
        ]);
    }

    public static function getNextPosition()
    {
        $lastPosition = self::waiting()->max('position');
        return $lastPosition ? $lastPosition + 1 : 1;
    }

    public static function getQueueLength()
    {
        return self::waiting()->count();
    }

    public static function getEstimatedWaitTime($position = null)
    {
        if (!$position) {
            $position = self::getQueueLength();
        }
        
        // Estimate 3 minutes per position (can be adjusted based on analytics)
        return $position * 3;
    }

    public function assignToAgent($agentId)
    {
        $this->update([
            'status' => 'assigned',
            'assigned_agent_id' => $agentId,
            'assigned_at' => now(),
            'wait_time_seconds' => now()->diffInSeconds($this->queued_at)
        ]);

        // Update conversation
        $this->conversation->update([
            'status' => 'active',
            'assigned_agent_id' => $agentId,
            'started_at' => now(),
            'queue_wait_time' => $this->wait_time_seconds
        ]);

        // Update agent status
        $agentStatus = AgentStatus::where('user_id', $agentId)->first();
        if ($agentStatus) {
            $agentStatus->increment('current_active_chats');
        }

        // Reorder queue positions
        self::reorderQueue();
    }

    public function complete($reason = 'resolved')
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        $this->conversation->update([
            'status' => 'completed',
            'ended_at' => now(),
            'end_reason' => $reason
        ]);

        // Update agent status
        if ($this->assigned_agent_id) {
            $agentStatus = AgentStatus::where('user_id', $this->assigned_agent_id)->first();
            if ($agentStatus) {
                $agentStatus->decrement('current_active_chats');
                $agentStatus->increment('total_chats_handled');
            }
        }
    }

    public static function reorderQueue()
    {
        $waitingChats = self::waiting()->orderBy('queued_at')->get();
        
        foreach ($waitingChats as $index => $chat) {
            $chat->update(['position' => $index + 1]);
        }
    }

    // Calculate wait time in minutes
    public function getWaitTimeAttribute()
    {
        if ($this->assigned_at) {
            return $this->assigned_at->diffInMinutes($this->queued_at);
        }
        
        return now()->diffInMinutes($this->queued_at);
    }

    // Get queue position for customer
    public function getPositionInQueueAttribute()
    {
        if ($this->status !== 'waiting') {
            return 0;
        }

        return self::waiting()
            ->where('queued_at', '<=', $this->queued_at)
            ->count();
    }
}