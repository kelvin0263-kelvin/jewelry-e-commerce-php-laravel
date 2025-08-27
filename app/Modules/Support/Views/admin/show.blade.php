@extends('layouts.admin')

@section('title', 'Ticket #' . $ticket->ticket_number)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $ticket->subject }}</h1>
                <p class="text-gray-600">Ticket #{{ $ticket->ticket_number }}</p>
            </div>
            <div class="flex flex-col items-end space-y-2">
                @php
                    $statusColors = [
                        'open' => 'bg-blue-100 text-blue-600',
                        'in_progress' => 'bg-yellow-100 text-yellow-600',
                        'waiting_customer' => 'bg-orange-100 text-orange-600',
                        'waiting_agent' => 'bg-purple-100 text-purple-600',
                        'resolved' => 'bg-green-100 text-green-600',
                        'closed' => 'bg-gray-100 text-gray-600',
                    ];
                    $priorityColors = [
                        'urgent' => 'bg-red-100 text-red-600',
                        'high' => 'bg-orange-100 text-orange-600',
                        'normal' => 'bg-blue-100 text-blue-600',
                        'low' => 'bg-gray-100 text-gray-600',
                    ];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ ucwords(str_replace('_', ' ', $ticket->status)) }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $priorityColors[$ticket->priority] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ ucfirst($ticket->priority) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Ticket Details Card -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Ticket Information</h3>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Customer</label>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $ticket->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $ticket->user->email }}</p>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Category</label>
                    <p class="text-gray-900">{{ $ticket->category_display }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Created</label>
                    <p class="text-gray-900">{{ $ticket->created_at->format('M j, Y g:i A') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Assigned to</label>
                    @if($ticket->assignedAgent)
                        <div class="flex items-center">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-2">
                                <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <p class="text-gray-900">{{ $ticket->assignedAgent->name }}</p>
                        </div>
                    @else
                        <div class="flex items-center text-red-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            Unassigned
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    @foreach($errors->all() as $error)
                        <p class="text-red-800">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Conversation -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Conversation</h3>
                    <p class="text-sm text-gray-600">Message history and replies</p>
                </div>
                
                <div class="p-4" style="max-height: 500px; overflow-y: auto;">
                    @forelse($ticket->replies()->with('user')->orderBy('created_at')->get() as $reply)
                        <div class="mb-6 {{ $reply->is_from_customer ? 'flex justify-end' : 'flex justify-start' }}">
                            <div class="max-w-lg {{ $reply->is_from_customer ? 'bg-blue-500 text-white' : ($reply->is_internal ? 'bg-yellow-50 border-l-4 border-yellow-400' : 'bg-gray-50') }} rounded-lg p-4">
                                @if($reply->is_internal)
                                    <div class="flex items-center mb-2">
                                        <svg class="w-4 h-4 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-yellow-800">Internal Note</span>
                                    </div>
                                @endif
                                
                                <p class="mb-3 {{ $reply->is_from_customer ? 'text-white' : 'text-gray-800' }}">{{ $reply->message }}</p>
                                
                                @if($reply->attachments)
                                    <div class="mb-3">
                                        @foreach($reply->attachments as $attachment)
                                            <a href="{{ route('tickets.download', ['path' => $attachment['path']]) }}" 
                                               class="inline-flex items-center text-sm {{ $reply->is_from_customer ? 'text-blue-100 hover:text-white' : 'text-blue-600 hover:text-blue-800' }} mb-1 mr-4">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                </svg>
                                                {{ $attachment['name'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                                
                                <div class="flex items-center justify-between text-xs {{ $reply->is_from_customer ? 'text-blue-100' : 'text-gray-500' }}">
                                    <span>{{ $reply->user->name }}</span>
                                    <span>{{ $reply->created_at->diffForHumans() }}</span>
                                </div>
                                @if($reply->is_first_response)
                                    <span class="inline-block mt-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">First Response</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <p class="text-gray-500">No replies yet. Be the first to respond!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Reply Form -->
            <div class="bg-white rounded-lg shadow-md mt-6">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Add Reply</h3>
                    <p class="text-sm text-gray-600">Respond to the customer or add internal notes</p>
                </div>
                
                <form action="{{ route('admin.tickets.reply', $ticket) }}" method="POST" enctype="multipart/form-data" class="p-4">
                    @csrf
                    <div class="mb-4">
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                        <textarea id="message" 
                                  name="message" 
                                  rows="4"
                                  class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Type your reply here..."
                                  required>{{ old('message') }}</textarea>
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="is_internal" 
                                   name="is_internal" 
                                   value="1"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                   {{ old('is_internal') ? 'checked' : '' }}>
                            <label for="is_internal" class="ml-2 flex items-center text-sm text-gray-700">
                                <svg class="w-4 h-4 text-yellow-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Internal Note (only visible to agents)
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">Attachments (optional)</label>
                        <input type="file" 
                               id="attachments" 
                               name="attachments[]" 
                               multiple
                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt"
                               class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Max 5MB per file. Allowed: jpg, png, pdf, doc, docx, txt</p>
                    </div>
                    
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                        Send Reply
                    </button>
                </form>
            </div>
        </div>

        <!-- Actions Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Quick Actions</h3>
                </div>
                <div class="p-4 space-y-3">
                    <a href="{{ route('admin.tickets.index') }}" class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition-colors inline-flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Tickets
                    </a>
                    
                    @if(!$ticket->is_escalated)
                        <form action="{{ route('admin.tickets.escalate', $ticket) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                Escalate
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Assignment -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Assignment</h3>
                </div>
                <div class="p-4">
                    <form action="{{ route('admin.tickets.assign', $ticket) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Agent</label>
                            <select name="agent_id" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Agent...</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" 
                                            {{ $ticket->assigned_agent_id == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Assign
                        </button>
                    </form>
                </div>
            </div>

            <!-- Status Management -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Status & Priority</h3>
                </div>
                <div class="p-4 space-y-4">
                    <!-- Status Update -->
                    <form action="{{ route('admin.tickets.status', $ticket) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="waiting_customer" {{ $ticket->status === 'waiting_customer' ? 'selected' : '' }}>Waiting Customer</option>
                                <option value="waiting_agent" {{ $ticket->status === 'waiting_agent' ? 'selected' : '' }}>Waiting Agent</option>
                                <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Note (optional)</label>
                            <textarea name="note" 
                                      class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                      rows="2" 
                                      placeholder="Status change note (optional)"></textarea>
                        </div>
                        
                        <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="5 13l4 4L19 7"></path>
                            </svg>
                            Update Status
                        </button>
                    </form>

                    <!-- Priority Update -->
                    <form action="{{ route('admin.tickets.priority', $ticket) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                            <select name="priority" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="normal" {{ $ticket->priority === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="w-full bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-md transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                            </svg>
                            Update Priority
                        </button>
                    </form>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Additional Information</h3>
                </div>
                <div class="p-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Contact Method</label>
                        <p class="text-gray-900">{{ ucwords(str_replace('_', ' ', $ticket->preferred_contact_method)) }}</p>
                        @if($ticket->contact_phone)
                            <p class="text-sm text-gray-500">Phone: {{ $ticket->contact_phone }}</p>
                        @endif
                    </div>
                    
                    @if($ticket->first_response_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">First Response</label>
                            <p class="text-gray-900">{{ $ticket->first_response_at->diffForHumans() }}</p>
                        </div>
                    @endif
                    
                    @if($ticket->is_escalated)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <p class="text-sm text-yellow-800">This ticket has been escalated</p>
                            </div>
                        </div>
                    @endif
                    
                    @if($ticket->satisfaction_rating)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Customer Rating</label>
                            <div class="flex items-center mb-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $ticket->satisfaction_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endfor
                                <span class="text-sm ml-2 text-gray-600">{{ $ticket->satisfaction_rating }}/5</span>
                            </div>
                            @if($ticket->customer_feedback)
                                <p class="text-sm text-gray-600 italic">"{{ $ticket->customer_feedback }}"</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection