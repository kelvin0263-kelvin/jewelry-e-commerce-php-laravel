@extends('layouts.admin')

@section('title', 'Ticket #' . $ticket->ticket_number)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="mb-2">{{ $ticket->subject }}</h4>
                            <p class="text-sm text-secondary mb-0">Ticket #{{ $ticket->ticket_number }}</p>
                        </div>
                        <div class="text-end">
                            <span class="badge {{ $ticket->status_badge_class }} mb-2">
                                {{ ucwords(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                            <br>
                            <span class="badge {{ $ticket->priority_badge_class }}">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Ticket Details -->
                <div class="card-body bg-gray-100">
                    <div class="row">
                        <div class="col-md-3">
                            <p class="text-xs text-uppercase font-weight-bolder mb-1">Customer</p>
                            <h6 class="mb-0">{{ $ticket->user->name }}</h6>
                            <p class="text-xs text-secondary">{{ $ticket->user->email }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-xs text-uppercase font-weight-bolder mb-1">Category</p>
                            <p class="text-sm mb-0">{{ $ticket->category_display }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-xs text-uppercase font-weight-bolder mb-1">Created</p>
                            <p class="text-sm mb-0">{{ $ticket->created_at->format('M j, Y g:i A') }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-xs text-uppercase font-weight-bolder mb-1">Assigned to</p>
                            @if($ticket->assignedAgent)
                                <p class="text-sm mb-0">{{ $ticket->assignedAgent->name }}</p>
                            @else
                                <p class="text-sm text-secondary mb-0">Unassigned</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Conversation -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Conversation</h6>
                </div>
                
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    @forelse($ticket->replies()->with('user')->orderBy('created_at')->get() as $reply)
                        <div class="d-flex mb-4 {{ $reply->is_from_customer ? 'justify-content-end' : 'justify-content-start' }}">
                            <div class="card {{ $reply->is_from_customer ? 'bg-primary text-white' : ($reply->is_internal ? 'bg-warning text-dark' : 'bg-light') }}" style="max-width: 80%;">
                                <div class="card-body p-3">
                                    @if($reply->is_internal)
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-lock text-warning me-2"></i>
                                            <small class="text-uppercase font-weight-bold">Internal Note</small>
                                        </div>
                                    @endif
                                    
                                    <p class="mb-2">{{ $reply->message }}</p>
                                    
                                    @if($reply->attachments)
                                        <div class="mt-2">
                                            @foreach($reply->attachments as $attachment)
                                                <a href="{{ route('tickets.download', ['path' => $attachment['path']]) }}" 
                                                   class="d-block text-xs {{ $reply->is_from_customer ? 'text-light' : 'text-primary' }}">
                                                    <i class="fas fa-paperclip me-1"></i>{{ $attachment['name'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    <div class="text-xs mt-2 {{ $reply->is_from_customer ? 'text-light' : 'text-secondary' }}">
                                        {{ $reply->user->name }} • {{ $reply->created_at->diffForHumans() }}
                                        @if($reply->is_first_response)
                                            <span class="badge badge-sm bg-success ms-2">First Response</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-comments fa-2x text-secondary mb-3"></i>
                            <p class="text-secondary">No replies yet. Be the first to respond!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Reply Form -->
            <div class="card mt-4">
                <div class="card-header pb-0">
                    <h6>Add Reply</h6>
                </div>
                
                <form action="{{ route('admin.tickets.reply', $ticket) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea id="message" 
                                      name="message" 
                                      rows="4"
                                      class="form-control"
                                      placeholder="Type your reply here..."
                                      required>{{ old('message') }}</textarea>
                        </div>
                        
                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox" 
                                       id="is_internal" 
                                       name="is_internal" 
                                       value="1"
                                       class="form-check-input"
                                       {{ old('is_internal') ? 'checked' : '' }}>
                                <label for="is_internal" class="form-check-label">
                                    <i class="fas fa-lock text-warning me-1"></i>
                                    Internal Note (only visible to agents)
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="attachments" class="form-label">Attachments (optional)</label>
                            <input type="file" 
                                   id="attachments" 
                                   name="attachments[]" 
                                   multiple
                                   accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt"
                                   class="form-control">
                            <small class="text-muted">Max 5MB per file. Allowed: jpg, png, pdf, doc, docx, txt</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-reply me-2"></i>Send Reply
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Actions Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary btn-sm w-100 mb-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Tickets
                    </a>
                    
                    @if(!$ticket->is_escalated)
                        <form action="{{ route('admin.tickets.escalate', $ticket) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm w-100">
                                <i class="fas fa-exclamation-triangle me-2"></i>Escalate
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Assignment -->
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Assignment</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.tickets.assign', $ticket) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="form-group mb-3">
                            <select name="agent_id" class="form-select form-select-sm">
                                <option value="">Select Agent...</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" 
                                            {{ $ticket->assigned_agent_id == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-info btn-sm w-100">
                            <i class="fas fa-user-check me-2"></i>Assign
                        </button>
                    </form>
                </div>
            </div>

            <!-- Status Management -->
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Status & Priority</h6>
                </div>
                <div class="card-body">
                    <!-- Status Update -->
                    <form action="{{ route('admin.tickets.status', $ticket) }}" method="POST" class="mb-3">
                        @csrf
                        @method('PATCH')
                        
                        <div class="form-group mb-2">
                            <label class="form-label text-xs">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="waiting_customer" {{ $ticket->status === 'waiting_customer' ? 'selected' : '' }}>Waiting Customer</option>
                                <option value="waiting_agent" {{ $ticket->status === 'waiting_agent' ? 'selected' : '' }}>Waiting Agent</option>
                                <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        
                        <div class="form-group mb-2">
                            <textarea name="note" 
                                      class="form-control form-control-sm" 
                                      rows="2" 
                                      placeholder="Status change note (optional)"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="fas fa-save me-2"></i>Update Status
                        </button>
                    </form>

                    <!-- Priority Update -->
                    <form action="{{ route('admin.tickets.priority', $ticket) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="form-group mb-2">
                            <label class="form-label text-xs">Priority</label>
                            <select name="priority" class="form-select form-select-sm">
                                <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="normal" {{ $ticket->priority === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-flag me-2"></i>Update Priority
                        </button>
                    </form>
                </div>
            </div>

            <!-- Ticket Info -->
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Ticket Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-xs text-uppercase font-weight-bolder mb-1">Contact Method</p>
                        <p class="text-sm mb-0">{{ ucwords(str_replace('_', ' ', $ticket->preferred_contact_method)) }}</p>
                        @if($ticket->contact_phone)
                            <p class="text-xs text-secondary">Phone: {{ $ticket->contact_phone }}</p>
                        @endif
                    </div>
                    
                    @if($ticket->first_response_at)
                        <div class="mb-3">
                            <p class="text-xs text-uppercase font-weight-bolder mb-1">First Response</p>
                            <p class="text-sm mb-0">{{ $ticket->first_response_at->diffForHumans() }}</p>
                        </div>
                    @endif
                    
                    @if($ticket->is_escalated)
                        <div class="alert alert-warning py-2 px-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <small>This ticket has been escalated</small>
                        </div>
                    @endif
                    
                    @if($ticket->satisfaction_rating)
                        <div class="mb-3">
                            <p class="text-xs text-uppercase font-weight-bolder mb-1">Customer Rating</p>
                            <div class="d-flex align-items-center mb-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $ticket->satisfaction_rating ? 'text-warning' : 'text-secondary' }}"></i>
                                @endfor
                                <span class="text-sm ms-2">{{ $ticket->satisfaction_rating }}/5</span>
                            </div>
                            @if($ticket->customer_feedback)
                                <p class="text-xs text-secondary">"{{ $ticket->customer_feedback }}"</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection