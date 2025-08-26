<?php

namespace App\Modules\Support\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Support\Models\Conversation;
use App\Modules\Support\Models\Message;
use App\Modules\Support\Models\ChatQueue;
use App\Modules\Support\Services\ChatQueueService;
use App\Modules\Support\Traits\EmitsChatEvents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Modules\Support\Events\MessageSent; // 我们很快会创建这个事件
use App\Modules\Support\Events\ConversationTerminated;
use App\Modules\Support\Services\ChatEventManager;
use Illuminate\Support\Facades\Broadcast;

class ChatController extends Controller
{
    use EmitsChatEvents;
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
     * Get conversation details
     */
    public function show(Conversation $conversation)
    {
        return response()->json($conversation->load(['user', 'agent']));
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

        // Use Observer pattern to handle message sent event
        $this->emitMessageSent($message->load('user'), $message->conversation);

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

        // Use Observer pattern to handle message sent event
        $this->emitMessageSent($message, $message->conversation);

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

        // Check for existing active or pending conversation
        $existingConversation = Conversation::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'active'])
            ->whereDoesntHave('queueItem', function($query) {
                $query->where('status', '!=', 'waiting');
            })
            ->first();
            
        // If there's an active conversation, return it
        if ($existingConversation && $existingConversation->status === 'active') {
            return response()->json([
                'conversation_id' => $existingConversation->id,
                'message' => 'You have an active conversation',
                'status' => 'active',
                'existing_conversation' => true
            ]);
        }

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

    /**
     * Terminate a conversation
     */
    public function terminateConversation($conversationId)
    {
        $user = Auth::user();
        
        // Find the conversation and validate permissions
        $conversation = Conversation::findOrFail($conversationId);
        
        // Check if user has permission to terminate this conversation
        if (!$user->is_admin && $conversation->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to terminate this conversation'
            ], 403);
        }

        // Check if conversation is already terminated
        if (in_array($conversation->status, ['completed', 'abandoned'])) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation is already terminated'
            ], 400);
        }

        // Update conversation status
        $conversation->update([
            'status' => 'completed',
            'ended_at' => now(),
            'end_reason' => $user->is_admin ? 'resolved' : 'abandoned'
        ]);

        // Update queue status if exists
        $queueItem = ChatQueue::where('conversation_id', $conversationId)->first();
        if ($queueItem) {
            $queueItem->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
        }

        // Update agent status to reduce active chats count
        if ($conversation->assigned_agent_id) {
            $agentStatus = \App\Modules\Support\Models\AgentStatus::where('user_id', $conversation->assigned_agent_id)->first();
            if ($agentStatus) {
                $agentStatus->decrement('current_active_chats');
            }
        }

        // Create a system message to indicate conversation termination
        $systemMessage = Message::create([
            'conversation_id' => $conversationId,
            'user_id' => null, // System message
            'body' => $user->is_admin 
                ? 'Conversation ended by agent.' 
                : 'Conversation ended by customer.',
            'message_type' => 'system'
        ]);

        // Use Observer pattern to handle events
        // Emit message sent event for system message
        $this->emitMessageSent($systemMessage, $conversation);
        
        // Emit conversation terminated event
        $this->emitConversationTerminated(
            $conversation->fresh(),
            $user->is_admin ? 'admin' : 'customer',
            $user->is_admin ? 'resolved' : 'abandoned'
        );

        return response()->json([
            'success' => true,
            'message' => 'Conversation terminated successfully',
            'conversation' => $conversation->fresh()
        ]);
    }
}