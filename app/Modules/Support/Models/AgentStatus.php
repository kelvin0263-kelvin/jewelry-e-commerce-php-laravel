<?php

namespace App\Modules\Support\Models;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentStatus extends Model
{
    use HasFactory;

    protected $table = 'agent_status';

    protected $fillable = [
        'user_id',
        'status',
        'max_concurrent_chats',
        'current_active_chats',
        'accepting_chats',
        'last_activity_at',
        'status_changed_at',
        'status_message',
        'skills',
        'total_chats_handled',
        'average_response_time',
        'customer_satisfaction_rating'
    ];

    protected $casts = [
        'accepting_chats' => 'boolean',
        'last_activity_at' => 'datetime',
        'status_changed_at' => 'datetime',
        'skills' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'online')
                    ->where('accepting_chats', true)
                    ->whereRaw('current_active_chats < max_concurrent_chats');
    }

    public function scopeOnline($query)
    {
        return $query->whereIn('status', ['online', 'busy']);
    }

    public function scopeByWorkload($query)
    {
        return $query->orderByRaw('(current_active_chats / max_concurrent_chats)');
    }

    // Static methods
    public static function getAvailableAgents()
    {
        return self::available()->byWorkload()->get();
    }

    public static function getNextAvailableAgent()
    {
        return self::available()->byWorkload()->first();
    }

    public static function getOnlineAgentsCount()
    {
        return self::online()->count();
    }

    public static function getTotalQueueCapacity()
    {
        return self::available()->sum('max_concurrent_chats') - 
               self::available()->sum('current_active_chats');
    }

    // Instance methods
    public function updateStatus($status, $message = null)
    {
        $this->update([
            'status' => $status,
            'status_changed_at' => now(),
            'status_message' => $message,
            'last_activity_at' => now()
        ]);
    }

    public function setAvailable($accepting = true)
    {
        $this->update([
            'status' => 'online',
            'accepting_chats' => $accepting,
            'status_changed_at' => now(),
            'last_activity_at' => now()
        ]);
    }

    public function setBusy()
    {
        $this->update([
            'status' => 'busy',
            'accepting_chats' => false,
            'status_changed_at' => now(),
            'last_activity_at' => now()
        ]);
    }

    public function setAway($message = null)
    {
        $this->update([
            'status' => 'away',
            'accepting_chats' => false,
            'status_message' => $message,
            'status_changed_at' => now(),
            'last_activity_at' => now()
        ]);
    }

    public function setOffline()
    {
        $this->update([
            'status' => 'offline',
            'accepting_chats' => false,
            'status_message' => null,
            'status_changed_at' => now()
        ]);
    }

    public function updateActivity()
    {
        $this->update(['last_activity_at' => now()]);
    }

    public function canAcceptChats()
    {
        return $this->status === 'online' && 
               $this->accepting_chats && 
               $this->current_active_chats < $this->max_concurrent_chats;
    }

    public function getWorkloadPercentage()
    {
        if ($this->max_concurrent_chats == 0) return 0;
        return ($this->current_active_chats / $this->max_concurrent_chats) * 100;
    }

    public function getRemainingCapacity()
    {
        return max(0, $this->max_concurrent_chats - $this->current_active_chats);
    }

    // Get status badge class for UI
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'online' => 'bg-green-100 text-green-800',
            'busy' => 'bg-yellow-100 text-yellow-800', 
            'away' => 'bg-orange-100 text-orange-800',
            'offline' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    // Get status icon
    public function getStatusIcon()
    {
        return match($this->status) {
            'online' => '🟢',
            'busy' => '🟡',
            'away' => '🟠', 
            'offline' => '⚫',
            default => '⚫'
        };
    }

    // Automatically set agent to busy if at capacity
    public function checkAndUpdateStatus()
    {
        if ($this->current_active_chats >= $this->max_concurrent_chats && $this->status === 'online') {
            $this->setBusy();
        } elseif ($this->current_active_chats < $this->max_concurrent_chats && $this->status === 'busy') {
            $this->setAvailable();
        }
    }
}