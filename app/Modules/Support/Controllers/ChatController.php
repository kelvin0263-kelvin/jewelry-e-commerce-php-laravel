<?php

namespace App\Modules\Support\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Support\Models\Conversation;
use App\Modules\Support\Models\Message;
use App\Modules\Support\Models\ChatQueue;
use App\Modules\Support\Services\ChatQueueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Modules\Support\Events\MessageSent; // 我们很快会创建这个事件
use Illuminate\Support\Facades\Broadcast;

class ChatController extends Controller
{
    /**
     * 获取当前管理员的所有聊天会话
     */
    public function conversations()
    {
        // Only show conversations that have been accepted by agents
        // Exclude conversations that are still pending in queue
        $conversations = Conversation::with('user')
            ->where(function($query) {
                $query->where('status', '!=', 'pending')
                      ->orWhereHas('queueItem', function($subQuery) {
                          $subQuery->where('status', 'assigned');
                      });
            })
            ->latest()
            ->get();
            
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

        // 广播新消息事件 - broadcast to all users in the channel
        broadcast(new MessageSent($message->load('user')));

        return response()->json($message->load('user'));
    }

    public function messages($id)
    {
        // Fetch messages for the conversation with user relationship
        $messages = Message::where('conversation_id', $id)
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
        $message = Message::create([
            'conversation_id' => $validated['conversation_id'],
            'user_id' => $validated['user_id'],
            'body' => $validated['content'], // Map 'content' to 'body'
        ]);

        // Load user relationship
        $message->load('user');

        // Broadcast the message for real-time updates - broadcast to all users in the channel
        broadcast(new MessageSent($message));

        // Return the message with user relationship
        return response()->json($message, 201);
    }

    /**
     * Start a new chat conversation and add to queue
     */
    public function startChat(Request $request)
    {
        $request->validate([
            'initial_message' => 'nullable|string|max:500',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'escalation_context' => 'nullable|array'
        ]);

        $user = Auth::user();
        $queueService = new ChatQueueService();

        // Check if user already has an existing queue item or pending conversation
        $existingQueueItem = ChatQueue::where('customer_id', $user->id)
            ->where('status', 'waiting')
            ->first();

        if ($existingQueueItem) {
            // Customer is already in queue, return their existing position
            $queueStatus = $queueService->getQueueStatus($existingQueueItem->conversation_id);
            
            return response()->json([
                'conversation_id' => $existingQueueItem->conversation_id,
                'queue_status' => $queueStatus,
                'message' => 'You are already in the chat queue',
                'position' => $queueStatus['position'],
                'estimated_wait' => $queueStatus['estimated_wait'],
                'existing_queue' => true
            ]);
        }

        // Check for existing pending conversation
        $existingConversation = Conversation::where('user_id', $user->id)
            ->where('status', 'pending')
            ->whereDoesntHave('queueItem', function($query) {
                $query->where('status', '!=', 'waiting');
            })
            ->first();

        if ($existingConversation) {
            $conversation = $existingConversation;
        } else {
            // Create new conversation only if no existing one
            $conversation = Conversation::create([
                'user_id' => $user->id,
                'status' => 'pending'
            ]);
        }

        // Add to queue with context from self-service if available
        $escalationContext = $request->escalation_context ?? session('chat_escalation_context');
        
        $queueItem = $queueService->addToQueue(
            $conversation->id,
            $user->id,
            $request->priority ?? 'normal',
            $escalationContext,
            $request->initial_message
        );

        // Clear escalation context from session
        session()->forget('chat_escalation_context');

        // Get queue status for customer
        $queueStatus = $queueService->getQueueStatus($conversation->id);

        return response()->json([
            'conversation_id' => $conversation->id,
            'queue_status' => $queueStatus,
            'message' => 'You have been added to the chat queue',
            'position' => $queueStatus['position'],
            'estimated_wait' => $queueStatus['estimated_wait']
        ]);
    }

    /**
     * Get queue status for customer
     */
    public function getQueueStatus($conversationId)
    {
        $queueService = new ChatQueueService();
        $status = $queueService->getQueueStatus($conversationId);
        
        return response()->json($status);
    }

    /**
     * Customer leaves queue
     */
    public function leaveQueue($conversationId)
    {
        $conversation = Conversation::where('id', $conversationId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $queueService = new ChatQueueService();
        $success = $queueService->abandonChat($conversationId);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Left queue successfully' : 'Failed to leave queue'
        ]);
    }
}