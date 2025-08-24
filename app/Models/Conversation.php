<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

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
        'queue_wait_time',
        'queue_id',
        'rating',
        'feedback'
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
}
