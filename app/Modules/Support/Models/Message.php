<?php

// relationship（Eloquent） 只影响查询，不会决定删除行为。

// migration 的 onDelete('cascade') 才决定是否级联删除。  

namespace App\Modules\Support\Models;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


// 模型名 Message → 表名 messages（小写 + 复数）。
class Message extends Model
{
     use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'conversation_id',
        'user_id',
        'body',
        'message_type',
        'read_at',
    ];


    // why define relationship !!!
    //$message = Message::find(1); 拿 id 1 的message
    // echo $message->user->name;  直接取发消息的用户
    // messages.user_id 去查 users.id and return a User Model Object and then get the user name.
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Check if message is a system message
     */
    public function isSystemMessage()
    {
        return $this->message_type === 'system';
    }

    /**
     * Scope to get only user messages
     */
    public function scopeUserMessages($query)
    {
        return $query->where('message_type', 'user');
    }

    /**
     * Scope to get only system messages
     */
    public function scopeSystemMessages($query)
    {
        return $query->where('message_type', 'system');
    }
}
