<?php

namespace App\Modules\Support\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Support\Models\Ticket;
use App\Modules\Support\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TicketController extends Controller
{
    // done (S)
    // (Multipurpose Internet Mail Extensions) not only checking extension but also the real content 
    // Allowed MIME types for attachments which is a white list of content can upload (server-side sniffed)
    private array $allowedMimes = [
        'image/jpeg', //image type with jpeg extension
        'image/png',
        'application/pdf',
        'text/plain',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    // done (s)
    // Centralized, secure upload handler – stores to private disk and returns metadata
    // parameter can be null or array type 
     private function secureStoreAttachments(?array $files): array{
        if (!$files) {
            return [];
        }

        $attachments = [];
        foreach ($files as $file) {
            // Extra server-side content sniffing
            $finfo = new \finfo(FILEINFO_MIME_TYPE); // create a new php built in finfo oject 
            $realMime = $finfo->file($file->getPathname()); // use the finfo to check the MIME type

            // Validate MIME against allowedMimes whitelist
            if (!in_array($realMime, $this->allowedMimes, true)) {
                throw ValidationException::withMessages([
                    'attachments' => 'One or more files have an unsupported type.', //if not valid throw this message
                ]);
            }

            // Basic image integrity check
            if (str_starts_with($realMime, 'image/')) {
                if (@getimagesize($file->getPathname()) === false) {
                    throw ValidationException::withMessages([
                        'attachments' => 'Invalid image file provided.',
                    ]);
                }
            }

            // Store on private disk (local) so files are not web-accessible
            // path prefix is tickets/attachments file name is hash value 
            $path = $file->store('tickets/attachments', 'local');

            $attachments[] = [
                'name' => $file->getClientOriginalName(), // file original name
                'path' => $path, // store path 
                'size' => $file->getSize(), // file size 
                'type' => $realMime, // the mime detected
            ];
        }

        return $attachments;
    }
    /**
     * Display customer's tickets
     */
    public function index()
    {
        $tickets = Ticket::where('user_id', Auth::id())
            ->with(['assignedAgent', 'replies'])
            ->latest()
            ->paginate(10);

        return view('support::tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new ticket
     */
    public function create()
    {
        $categories = Ticket::getCategories();
        $priorities = Ticket::getPriorities();

        return view('support::tickets.create', compact('categories', 'priorities'));
    }

    /**
     * done(s)
     * Store a newly created ticket
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255', //less than 255 character
            'description' => 'required|string|max:5000',
            'category' => 'required|in:' . implode(',', array_keys(Ticket::getCategories())), // must under predefined category
            'priority' => 'required|in:' . implode(',', array_keys(Ticket::getPriorities())),
            'contact_email' => 'nullable|email', // can be null 
            'contact_phone' => 'nullable|string|max:20',
            'preferred_contact_method' => 'required|in:email,phone,portal', // only the 3 value 
            'attachments' => 'nullable|array|max:5', // max is 5 only
            'attachments.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx,txt|mimetypes:image/jpeg,image/png,application/pdf,text/plain,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document' //must less than 5mb and must fullfill mimetype and extension
        ]);

        // Handle file uploads securely (private storage + server-side sniffing) and return an array which contains file name / path / size / type
        $attachments = $this->secureStoreAttachments($request->file('attachments'));

        // Create the ticket in database
        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'contact_email' => $validated['contact_email'] ?? Auth::user()->email,
            'contact_phone' => $validated['contact_phone'],
            'preferred_contact_method' => $validated['preferred_contact_method'],
            'metadata' => [
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
                'attachments' => $attachments
            ]
        ]);

        // Create initial system message
        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => "Ticket created by customer.\n\n" . $validated['description'],
            'reply_type' => 'system',
            'attachments' => $attachments
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Ticket #{$ticket->ticket_number} has been created successfully! We'll get back to you soon.");
    }

    /**
     * Display the specified ticket
     */
    public function show(Ticket $ticket)
    {
        // Ensure user can only view their own tickets
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'You can only view your own tickets.');
        }

        $ticket->load(['assignedAgent', 'replies.user']);

        // Mark customer replies as read by customer
        $ticket->replies()
            ->where('reply_type', 'agent')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('support::tickets.show', compact('ticket'));
    }

    /**
     * Done(s)
     * Add a reply to the ticket
     */
    public function reply(Request $request, Ticket $ticket)
    {
        // Ensure user can only reply to their own tickets
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'You can only reply to your own tickets.');
        }

        // Validate that ticket is not closed
        if (in_array($ticket->status, ['resolved', 'closed'])) {
            throw ValidationException::withMessages([
                'message' => 'You cannot reply to a resolved or closed ticket. Please create a new ticket if you need further assistance.'
            ]);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx,txt|mimetypes:image/jpeg,image/png,application/pdf,text/plain,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]);

        // Handle file uploads securely
        $attachments = $this->secureStoreAttachments($request->file('attachments'));

        // Create the reply
        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'reply_type' => 'customer',
            'attachments' => $attachments
        ]);

        return back()->with('success', 'Your reply has been added successfully.');
    }

    /**
     * Rate the ticket (customer satisfaction)
     */
    public function rate(Request $request, Ticket $ticket)
    {
        // Ensure user can only rate their own tickets
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'You can only rate your own tickets.');
        }

        // Validate that ticket is resolved
        if ($ticket->status !== 'resolved') {
            throw ValidationException::withMessages([
                'rating' => 'You can only rate resolved tickets.'
            ]);
        }

        $validated = $request->validate([
            'satisfaction_rating' => 'required|integer|between:1,5',
            'customer_feedback' => 'nullable|string|max:1000'
        ]);

        $ticket->update([
            'satisfaction_rating' => $validated['satisfaction_rating'],
            'customer_feedback' => $validated['customer_feedback']
        ]);

        return back()->with('success', 'Thank you for your feedback!');
    }

    /**
     * Close the ticket (customer can close their own tickets)
     */
    public function close(Ticket $ticket)
    {
        // Ensure user can only close their own tickets
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'You can only close your own tickets.');
        }

        if (in_array($ticket->status, ['resolved', 'closed'])) {
            return back()->with('info', 'This ticket is already closed.');
        }

        $ticket->close();

        // Add system message
        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => 'Ticket closed by customer.',
            'reply_type' => 'system'
        ]);

        return back()->with('success', 'Ticket has been closed.');
    }

    /**
     * Reopen a closed ticket
     */
    public function reopen(Ticket $ticket)
    {
        // Ensure user can only reopen their own tickets
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'You can only reopen your own tickets.');
        }

        if (!in_array($ticket->status, ['resolved', 'closed'])) {
            return back()->with('info', 'This ticket is already open.');
        }

        $ticket->reopen();

        // Add system message
        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => 'Ticket reopened by customer.',
            'reply_type' => 'system'
        ]);

        return back()->with('success', 'Ticket has been reopened.');
    }

    /**
     * done (s)
     * Download attachment
     */
    public function downloadAttachment(Request $request)
    {
        // get the request path value
        $path = $request->query('path');

        // First prefer private disk (local). Fallback to public for backward compatibility
        $onLocal = $path && Storage::disk('local')->exists($path);
        $onPublic = $path && Storage::disk('public')->exists($path); // by default not have but for old data
        if (!$path || (!$onLocal && !$onPublic)) {
            abort(404, 'File not found.');
        }

        // Additional security: ensure the file belongs to a ticket the caller can access
        $user = Auth::user();

        if ($user && ($user->is_admin ?? false)) { // for any admin
            // Admin access: allow if the file is attached to any ticket reply
            // Optionally restrict to assigned tickets: add ->where('assigned_agent_id', $user->id)
            $ticket = Ticket::whereHas('replies', function ($query) use ($path) {
                $query->whereJsonContains('attachments', ['path' => $path]);
            })->first();
        } else {
            // Customer access: only if the file belongs to their own ticket
            $ticket = Ticket::where('user_id', $user?->id)
                ->whereHas('replies', function ($query) use ($path) {
                    $query->whereJsonContains('attachments', ['path' => $path]);
                })
                ->first();
        }

        // after validation still not have reject 
        if (!$ticket) {
            abort(403, 'You do not have permission to download this file.');
        }

        //Storage::disk()->download($path) 会自动生成一个 HTTP 响应，触发浏览器下载。
        return $onLocal
            ? Storage::disk('local')->download($path)
            : Storage::disk('public')->download($path);
    }
}
