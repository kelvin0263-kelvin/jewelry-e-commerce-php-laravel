<?php

namespace App\Modules\Support\Models;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     use HasFactory;
    protected $fillable = [
        'user_id',
        'admin_id',
        'status',
        'assigned_agent_id',
        'started_at',
        'ended_at',
        'end_reason',
        'queue_wait_time',
        'queue_id',
        'customer_rating',
        'customer_feedback'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'customer_rating' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function queueItem()
    {
        return $this->hasOne(ChatQueue::class, 'conversation_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    /**
     * Check if conversation is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if conversation is terminated
     */
    public function isTerminated()
    {
        return in_array($this->status, ['completed', 'abandoned']);
    }

    /**
     * Scope for active conversations
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for terminated conversations
     */
    public function scopeTerminated($query)
    {
        return $query->whereIn('status', ['completed', 'abandoned']);
    }
}
