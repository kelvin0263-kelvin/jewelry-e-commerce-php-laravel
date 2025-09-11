<?php

namespace App\Modules\Support\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Support\Models\Conversation;
use App\Modules\Support\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatHistoryController extends Controller
{
    /**
     * Display customer's chat history
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get all conversations for the current user, ordered by most recent
        $conversations = Conversation::with(['agent', 'messages' => function($query) {
                $query->latest()->limit(1); // Get the last message for preview
            }])
            ->where('user_id', $user->id)
            ->whereIn('status', ['active', 'completed', 'abandoned']) // Exclude pending conversations
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        // Use the Support module's view namespace
        return view('support::chat-history.index', compact('conversations'));
    }

    /**
     * Show a specific conversation with all messages
     */
    public function show($conversationId)
    {
        $user = Auth::user();
        
        // Get the conversation with messages and agent info
        $conversation = Conversation::with(['agent', 'messages.user'])
            ->where('id', $conversationId)
            ->where('user_id', $user->id) // Ensure user can only view their own conversations
            ->firstOrFail();

        // Mark messages as read (optional)
        Message::where('conversation_id', $conversationId)
            ->whereNull('read_at')
            ->where('user_id', '!=', $user->id) // Only mark messages from others as read
            ->update(['read_at' => now()]);

        // Use the Support module's view namespace
        return view('support::chat-history.show', compact('conversation'));
    }

    /**
     * Get conversation messages as JSON (for AJAX loading)
     */
    public function messages($conversationId)
    {
        $user = Auth::user();
        
        // Verify the conversation belongs to the user
        $conversation = Conversation::where('id', $conversationId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Get all messages for the conversation
        $messages = Message::with('user')
            ->where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    /**
     * Search conversations
     */
    public function search(Request $request)
    {
        $user = Auth::user();
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return redirect()->route('chat-history.index');
        }

        // Search in conversation messages
        $conversations = Conversation::with(['agent', 'messages' => function($messageQuery) {
                $messageQuery->latest()->limit(1);
            }])
            ->where('user_id', $user->id)
            ->whereIn('status', ['active', 'completed', 'abandoned'])
            ->whereHas('messages', function($messageQuery) use ($query) {
                $messageQuery->where('body', 'like', '%' . $query . '%');
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        // Use the Support module's view namespace
        return view('support::chat-history.index', compact('conversations', 'query'));
    }

    /**
     * Download conversation transcript
     */
    public function download($conversationId)
    {
        $user = Auth::user();
        
        // Get the conversation with messages
        $conversation = Conversation::with(['agent', 'messages.user'])
            ->where('id', $conversationId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $transcript = $this->generateTranscript($conversation);
        
        $filename = 'chat-transcript-' . $conversationId . '-' . now()->format('Y-m-d') . '.txt';
        
        return response($transcript)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Generate text transcript of conversation
     */
    private function generateTranscript($conversation)
    {
        $transcript = "Chat Transcript\n";
        $transcript .= "================\n";
        $transcript .= "Conversation ID: " . $conversation->id . "\n";
        $transcript .= "Customer: " . $conversation->user->name . " (" . $conversation->user->email . ")\n";
        $transcript .= "Agent: " . ($conversation->agent ? $conversation->agent->name : 'Unassigned') . "\n";
        $transcript .= "Started: " . $conversation->created_at->format('Y-m-d H:i:s') . "\n";
        $transcript .= "Status: " . ucfirst($conversation->status) . "\n";
        if ($conversation->ended_at) {
            $transcript .= "Ended: " . $conversation->ended_at->format('Y-m-d H:i:s') . "\n";
        }
        $transcript .= "\nMessages:\n";
        $transcript .= "---------\n\n";

        foreach ($conversation->messages as $message) {
            $sender = $message->message_type === 'system' 
                ? 'System' 
                : ($message->user ? $message->user->name : 'Unknown');
                
            $timestamp = $message->created_at->format('Y-m-d H:i:s');
            $transcript .= "[{$timestamp}] {$sender}: {$message->body}\n\n";
        }

        $transcript .= "\n--- End of Transcript ---\n";
        
        return $transcript;
    }
}
