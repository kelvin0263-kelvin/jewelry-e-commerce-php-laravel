<?php
// app/Modules/Support/Controllers/Api/TicketController.php
namespace App\Modules\Support\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Support\Models\Ticket;
use App\Modules\Support\Models\TicketReply;
use App\Modules\Support\Resources\TicketResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    /**
     * Get user's tickets
     */
    public function index(Request $request)
    {
        try {
            $query = Ticket::where('user_id', Auth::id())->latest();
            
            // Filter by status
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }
            
            // Filter by priority
            if ($request->has('priority') && $request->priority !== 'all') {
                $query->where('priority', $request->priority);
            }
            
            // Search by subject or content
            if ($request->has('search') && !empty($request->search)) {
                $query->where(function($q) use ($request) {
                    $q->where('subject', 'like', '%' . $request->search . '%')
                      ->orWhere('content', 'like', '%' . $request->search . '%');
                });
            }
            
            $tickets = $query->paginate($request->get('per_page', 15));
            
            return TicketResource::collection($tickets);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve tickets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new ticket
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'subject' => 'required|string|max:255',
                'content' => 'required|string',
                'category' => 'required|string|in:general,technical,billing,complaint,feature_request',
                'priority' => 'sometimes|string|in:low,medium,high,urgent',
            ]);

            $validated['user_id'] = Auth::id();
            $validated['status'] = 'open';
            $validated['priority'] = $validated['priority'] ?? 'medium';

            $ticket = Ticket::create($validated);
            
            return (new TicketResource($ticket))
                    ->response()
                    ->setStatusCode(201)
                    ->header('Location', route('api.tickets.show', $ticket));
                    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create ticket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific ticket
     */
    public function show(Ticket $ticket)
    {
        try {
            // Ensure user can only view their own tickets
            if ($ticket->user_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to view this ticket'
                ], 403);
            }

            $ticket->load(['replies.user', 'assignedAgent']);
            
            return new TicketResource($ticket);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve ticket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a ticket
     */
    public function update(Request $request, Ticket $ticket): JsonResponse
    {
        try {
            // Ensure user can only update their own tickets
            if ($ticket->user_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to update this ticket'
                ], 403);
            }

            // Users can only update certain fields
            $validated = $request->validate([
                'subject' => 'sometimes|string|max:255',
                'priority' => 'sometimes|string|in:low,medium,high,urgent',
            ]);

            $ticket->update($validated);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Ticket updated successfully',
                'data' => new TicketResource($ticket->refresh())
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update ticket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reply to a ticket
     */
    public function reply(Request $request, Ticket $ticket): JsonResponse
    {
        try {
            // Ensure user can only reply to their own tickets
            if ($ticket->user_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to reply to this ticket'
                ], 403);
            }

            $validated = $request->validate([
                'content' => 'required|string',
            ]);

            $reply = $ticket->replies()->create([
                'content' => $validated['content'],
                'user_id' => Auth::id(),
                'is_admin_reply' => false
            ]);

            // Update ticket status to 'awaiting_agent' if it was closed
            if ($ticket->status === 'closed') {
                $ticket->update(['status' => 'awaiting_agent']);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Reply added successfully',
                'data' => [
                    'reply_id' => $reply->id,
                    'ticket_status' => $ticket->refresh()->status
                ]
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add reply',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Close a ticket
     */
    public function close(Ticket $ticket): JsonResponse
    {
        try {
            // Ensure user can only close their own tickets
            if ($ticket->user_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to close this ticket'
                ], 403);
            }

            $ticket->update(['status' => 'closed']);

            return response()->json([
                'status' => 'success',
                'message' => 'Ticket closed successfully',
                'data' => new TicketResource($ticket->refresh())
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to close ticket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get FAQ items
     */
    public function faq(): JsonResponse
    {
        try {
            // This would typically come from a database table
            $faqItems = [
                [
                    'id' => 1,
                    'category' => 'General',
                    'question' => 'How do I track my order?',
                    'answer' => 'You can track your order by logging into your account and visiting the Orders section.'
                ],
                [
                    'id' => 2,
                    'category' => 'Billing',
                    'question' => 'What payment methods do you accept?',
                    'answer' => 'We accept all major credit cards, PayPal, and bank transfers.'
                ],
                [
                    'id' => 3,
                    'category' => 'Shipping',
                    'question' => 'How long does shipping take?',
                    'answer' => 'Standard shipping takes 3-5 business days, while express shipping takes 1-2 business days.'
                ]
            ];

            return response()->json([
                'status' => 'success',
                'data' => $faqItems
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve FAQ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get ticket categories
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = [
                ['value' => 'general', 'label' => 'General Inquiry'],
                ['value' => 'technical', 'label' => 'Technical Support'],
                ['value' => 'billing', 'label' => 'Billing & Payment'],
                ['value' => 'complaint', 'label' => 'Complaint'],
                ['value' => 'feature_request', 'label' => 'Feature Request']
            ];

            return response()->json([
                'status' => 'success',
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}