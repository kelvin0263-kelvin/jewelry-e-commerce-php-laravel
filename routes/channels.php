<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;


Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// 添加这个新的频道授权
Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    return true; // TEMPORARY: allow all authenticated users for testing
});