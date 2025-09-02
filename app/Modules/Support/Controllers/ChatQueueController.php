<?php

namespace App\Modules\Support\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Support\Models\ChatQueue;
use App\Modules\Support\Models\AgentStatus;
use App\Modules\Support\Services\ChatQueueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatQueueController extends Controller
{
    protected $queueService;

    public function __construct(ChatQueueService $queueService)
    {
        $this->queueService = $queueService;
    }

    /**
     * Display chat queue dashboard
     * Initial viewing and return view 
     */
    public function index()
    {
        $pendingChats = $this->queueService->getPendingChats();
        $stats = $this->queueService->getQueueStats();
        $agents = AgentStatus::with('user')->get();
        
        return view('support::admin.chat-queue.index', compact('pendingChats', 'stats', 'agents'));
    }

    /**
     * Get queue data for AJAX updates
     * Route line 174
     */
    public function getData()
    {
        return response()->json([
            'pending_chats' => $this->queueService->getPendingChats(),
            'stats' => $this->queueService->getQueueStats(),
            'agents' => AgentStatus::with('user')->get()
        ]);
    }

    /**
     * Accept a chat from the queue
     * Route line 175
     */
    public function acceptChat(Request $request, $queueId)
    {
        $queueItem = ChatQueue::findOrFail($queueId);
        
        // Check if current user can accept chats
        $agentStatus = AgentStatus::where('user_id', Auth::id())->first();
        if (!$agentStatus || !$agentStatus->canAcceptChats()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot accept chats at this time. Check your status.'
            ], 400);
        }

        $success = $this->queueService->assignChatToAgent($queueItem, Auth::id());

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Chat accepted successfully',
                'conversation_id' => $queueItem->conversation_id
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to accept chat'
        ], 400);
    }

    /**
     * Assign chat to specific agent
     * Route line 176
     */
    public function assignChat(Request $request, $queueId)
    {
        $request->validate([
            'agent_id' => 'required|exists:users,id'
        ]);

        $queueItem = ChatQueue::findOrFail($queueId);
        $success = $this->queueService->assignChatToAgent($queueItem, $request->agent_id);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Chat assigned successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to assign chat'
        ], 400);
    }

    /**
     * Transfer chat to another agent
     */
    public function transferChat(Request $request, $conversationId)
    {
        $request->validate([
            'agent_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:255'
        ]);

        $success = $this->queueService->transferChat(
            $conversationId, 
            $request->agent_id, 
            $request->reason
        );

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Chat transferred successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to transfer chat'
        ], 400);
    }

    /**
     * Complete a chat
     */
    public function completeChat(Request $request, $conversationId)
    {
        $request->validate([
            'reason' => 'required|in:resolved,transferred,abandoned,timeout',
            'rating' => 'nullable|numeric|min:1|max:5',
            'feedback' => 'nullable|string|max:1000'
        ]);

        $feedback = null;
        if ($request->rating || $request->feedback) {
            $feedback = [
                'rating' => $request->rating,
                'feedback' => $request->feedback
            ];
        }

        $success = $this->queueService->completeChat(
            $conversationId, 
            $request->reason,
            $feedback
        );

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Chat completed successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to complete chat'
        ], 400);
    }

    /**
     * Update agent status
     * Route line 183
     */
    public function updateAgentStatus(Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:online,busy,away,offline',
            'accepting_chats' => 'boolean',
            'status_message' => 'nullable|string|max:255',
            'current_active_chats' => 'integer|min:0',
            'force_reset' => 'boolean'
        ]);

        $agentStatus = AgentStatus::firstOrCreate(
            ['user_id' => Auth::id()],
            ['max_concurrent_chats' => 3]
        );

        // Update basic status
        $agentStatus->updateStatus(
            $validated['status'],
            $validated['status_message'] ?? null
        );

        // Update accepting_chats flag
        if ($request->has('accepting_chats')) {
            $agentStatus->update(['accepting_chats' => $validated['accepting_chats']]);
        }

        // Force reset current active chats if requested
        if ($request->has('force_reset') && $validated['force_reset']) {
            $agentStatus->update([
                'current_active_chats' => $validated['current_active_chats'] ?? 0,
                'accepting_chats' => $validated['accepting_chats'] ?? true
            ]);
        }

        // Refresh the model to get updated values
        $agentStatus->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'agent_status' => [
                'status' => $agentStatus->status,
                'accepting_chats' => $agentStatus->accepting_chats,
                'current_active_chats' => $agentStatus->current_active_chats,
                'can_accept_chats' => $agentStatus->canAcceptChats()
            ]
        ]);
    }

    /**
     * Get agent's active chats
     */
    public function getMyChats()
    {
        $activeChats = $this->queueService->getActiveChatsForAgent(Auth::id());
        
        return response()->json([
            'active_chats' => $activeChats,
            'count' => $activeChats->count()
        ]);
    }

    /**
     * Get queue statistics
     */
    public function getStats()
    {
        return response()->json($this->queueService->getQueueStats());
    }

    /**
     * Abandon chat (remove from queue)
     * Route line 177
     */
    public function abandonChat($queueId)
    {
        $queueItem = ChatQueue::findOrFail($queueId);
        $success = $this->queueService->abandonChat($queueItem->conversation_id);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Chat abandoned successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to abandon chat'
        ], 400);
    }

    /**
     * Get queue position for customer
     */
    public function getQueueStatus($conversationId)
    {
        $status = $this->queueService->getQueueStatus($conversationId);
        return response()->json($status);
    }

}