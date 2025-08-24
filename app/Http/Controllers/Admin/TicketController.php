<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    /**
     * Display admin ticket dashboard
     */
    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'assignedAgent', 'replies']);

        // Apply filters
        if ($request->filled('status')) {
            if ($request->status === 'open') {
                $query->open();
            } elseif ($request->status === 'closed') {
                $query->closed();
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('assigned_to')) {
            if ($request->assigned_to === 'unassigned') {
                $query->unassigned();
            } elseif ($request->assigned_to === 'me') {
                $query->assignedTo(Auth::id());
            } else {
                $query->assignedTo($request->assigned_to);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Sort by priority and created date
        $tickets = $query->byPriority()
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Get statistics
        $stats = $this->getTicketStats();

        // Get agents for assignment
        $agents = User::where('is_admin', true)->get();

        return view('admin.tickets.index', compact('tickets', 'stats', 'agents'));
    }

    /**
     * Show ticket details for admin
     */
    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'assignedAgent', 'replies.user']);
        
        // Mark agent replies as read by admin
        $ticket->replies()
            ->where('reply_type', 'customer')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $agents = User::where('is_admin', true)->get();

        return view('admin.tickets.show', compact('ticket', 'agents'));
    }

    /**
     * Assign ticket to agent
     */
    public function assign(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'agent_id' => 'required|exists:users,id'
        ]);

        $agent = User::find($validated['agent_id']);
        
        if (!$agent->is_admin) {
            return back()->with('error', 'Selected user is not an admin.');
        }

        $ticket->assignToAgent($validated['agent_id']);

        // Add system message
        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => "Ticket assigned to {$agent->name}.",
            'reply_type' => 'system',
            'is_internal' => true
        ]);

        return back()->with('success', "Ticket assigned to {$agent->name}.");
    }

    /**
     * Add reply to ticket (agent)
     */
    public function reply(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'is_internal' => 'boolean',
            'attachments.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx,txt'
        ]);

        // Handle file uploads
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets/attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType()
                ];
            }
        }

        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'reply_type' => 'agent',
            'is_internal' => $validated['is_internal'] ?? false,
            'attachments' => $attachments
        ]);

        $messageType = $validated['is_internal'] ? 'internal note' : 'reply';
        return back()->with('success', "Your {$messageType} has been added.");
    }

    /**
     * Update ticket status
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,waiting_customer,waiting_agent,resolved,closed',
            'note' => 'nullable|string|max:500'
        ]);

        $oldStatus = $ticket->status;
        $newStatus = $validated['status'];

        // Update ticket
        if ($newStatus === 'resolved') {
            $ticket->markAsResolved(Auth::id());
        } elseif ($newStatus === 'closed') {
            $ticket->close(Auth::id());
        } else {
            $ticket->update(['status' => $newStatus]);
        }

        // Add system message
        $message = "Ticket status changed from '{$oldStatus}' to '{$newStatus}'.";
        if ($validated['note']) {
            $message .= "\nNote: " . $validated['note'];
        }

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $message,
            'reply_type' => 'system',
            'is_internal' => true
        ]);

        return back()->with('success', 'Ticket status updated.');
    }

    /**
     * Update ticket priority
     */
    public function updatePriority(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'priority' => 'required|in:low,normal,high,urgent'
        ]);

        $oldPriority = $ticket->priority;
        $newPriority = $validated['priority'];

        $ticket->update(['priority' => $newPriority]);

        // Add system message
        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => "Ticket priority changed from '{$oldPriority}' to '{$newPriority}'.",
            'reply_type' => 'system',
            'is_internal' => true
        ]);

        return back()->with('success', 'Ticket priority updated.');
    }

    /**
     * Escalate ticket
     */
    public function escalate(Ticket $ticket)
    {
        if ($ticket->is_escalated) {
            return back()->with('info', 'This ticket is already escalated.');
        }

        $ticket->escalate();

        // Add system message
        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => 'Ticket escalated by ' . Auth::user()->name . '. Priority increased to ' . $ticket->priority . '.',
            'reply_type' => 'system',
            'is_internal' => true
        ]);

        return back()->with('success', 'Ticket has been escalated.');
    }

    /**
     * Get ticket statistics
     */
    public function getStats()
    {
        return response()->json($this->getTicketStats());
    }

    /**
     * Calculate ticket statistics
     */
    private function getTicketStats(): array
    {
        return [
            'total' => Ticket::count(),
            'open' => Ticket::open()->count(),
            'closed' => Ticket::closed()->count(),
            'unassigned' => Ticket::unassigned()->count(),
            'overdue' => Ticket::open()->get()->filter(function ($ticket) {
                return $ticket->is_overdue;
            })->count(),
            'my_tickets' => Ticket::assignedTo(Auth::id())->open()->count(),
            'urgent' => Ticket::where('priority', 'urgent')->open()->count(),
            'avg_response_time' => Ticket::whereNotNull('response_time_hours')
                ->avg('response_time_hours'),
            'avg_resolution_time' => Ticket::whereNotNull('resolution_time_hours')
                ->avg('resolution_time_hours'),
            'satisfaction_avg' => Ticket::whereNotNull('satisfaction_rating')
                ->avg('satisfaction_rating'),
            'by_category' => Ticket::select('category', DB::raw('count(*) as count'))
                ->groupBy('category')
                ->pluck('count', 'category')
                ->toArray(),
            'by_priority' => Ticket::select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->pluck('count', 'priority')
                ->toArray(),
        ];
    }

    /**
     * Bulk actions on tickets
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:assign,close,resolve,escalate,delete',
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'exists:tickets,id',
            'agent_id' => 'nullable|exists:users,id'
        ]);

        $tickets = Ticket::whereIn('id', $validated['ticket_ids'])->get();
        $count = $tickets->count();

        foreach ($tickets as $ticket) {
            switch ($validated['action']) {
                case 'assign':
                    if ($validated['agent_id']) {
                        $ticket->assignToAgent($validated['agent_id']);
                    }
                    break;
                case 'close':
                    $ticket->close(Auth::id());
                    break;
                case 'resolve':
                    $ticket->markAsResolved(Auth::id());
                    break;
                case 'escalate':
                    if (!$ticket->is_escalated) {
                        $ticket->escalate();
                    }
                    break;
                case 'delete':
                    $ticket->delete();
                    break;
            }
        }

        return back()->with('success', "Bulk action '{$validated['action']}' applied to {$count} tickets.");
    }
}