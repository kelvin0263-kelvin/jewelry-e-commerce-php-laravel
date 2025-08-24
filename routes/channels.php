<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Modules.User.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// 添加这个新的频道授权
Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    Log::info('=== CHANNEL AUTH START ===');
    Log::info('Channel auth check for chat.' . $conversationId, [
        'user_provided' => $user !== null,
        'user_id' => $user ? $user->id : 'NO USER',
        'user_email' => $user ? $user->email : 'NO EMAIL',
        'is_authenticated_via_auth_check' => Auth::check(),
        'auth_user_id' => Auth::id(),
        'conversation_id' => $conversationId,
        'request_ip' => request()->ip(),
        'user_agent' => request()->userAgent(),
    ]);
    
    // Check if user is authenticated
    if (!$user) {
        Log::error('Channel auth FAILED: No user provided to callback');
        Log::error('This usually means the user is not authenticated when calling /broadcasting/auth');
        return false;
    }
    
    // For testing: allow all authenticated users
    Log::info('Channel auth SUCCESS for user: ' . $user->id . ' (' . $user->email . ')');
    Log::info('=== CHANNEL AUTH END ===');
    return true; // TEMPORARY: allow all authenticated users for testing
});