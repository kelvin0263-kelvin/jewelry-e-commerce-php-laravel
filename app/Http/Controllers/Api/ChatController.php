<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent; // 我们很快会创建这个事件
use Illuminate\Support\Facades\Broadcast;

class ChatController extends Controller
{
    /**
     * 获取当前管理员的所有聊天会话
     */
    public function conversations()
    {
        // 这里可以添加更复杂的逻辑，比如只显示有新消息的会话
        $conversations = Conversation::with('user')->latest()->get();
        return response()->json($conversations);
    }

    /**
     * 获取指定会话的所有消息
     */
    public function fetchMessages(Conversation $conversation)
    {
        // 授权检查：确保当前用户可以查看此会话
        // (我们稍后会完善这部分)
        return response()->json($conversation->messages()->with('user')->get());
    }

    /**
     * 发送新消息
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'body' => 'required|string',
        ]);

        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'user_id' => Auth::id(), // 发送者是当前登录的用户
            'body' => $request->body,
        ]);

        // 广播新消息事件
        broadcast(new MessageSent($message->load('user')))->toOthers();

        return response()->json($message->load('user'));
    }

    public function messages($id)
    {
        // Fetch messages for the conversation with user relationship
        $messages = \App\Models\Message::where('conversation_id', $id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();
        return response()->json($messages);
    }

    public function store(Request $request)
    {
        // Validate and store the message
        $validated = $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        // Create message with 'body' field (matching your database schema)
        $message = \App\Models\Message::create([
            'conversation_id' => $validated['conversation_id'],
            'user_id' => $validated['user_id'],
            'body' => $validated['content'], // Map 'content' to 'body'
        ]);

        // Load user relationship
        $message->load('user');

        // Broadcast the message for real-time updates
        broadcast(new MessageSent($message))->toOthers();

        // Return the message with user relationship
        return response()->json($message, 201);
    }
}