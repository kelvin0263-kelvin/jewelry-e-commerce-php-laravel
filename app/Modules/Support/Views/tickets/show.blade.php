@extends('layouts.app')

@section('title', 'Ticket #' . $ticket->ticket_number)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ $ticket->subject }}</h1>
                        <p class="text-gray-600 mt-1">Ticket #{{ $ticket->ticket_number }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $ticket->status_badge_class }} text-white">
                            {{ ucwords(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                        <div class="mt-2">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $ticket->priority_badge_class }} text-white">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ticket Details -->
            <div class="p-6 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-600">Category:</span>
                        <p class="text-gray-800">{{ $ticket->category_display }}</p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Created:</span>
                        <p class="text-gray-800">{{ $ticket->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Assigned to:</span>
                        <p class="text-gray-800">
                            @if($ticket->assignedAgent)
                                {{ $ticket->assignedAgent->name }}
                            @else
                                <span class="text-gray-400">Unassigned</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Conversation -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Conversation</h2>
            </div>
            
            <div class="p-6 max-h-96 overflow-y-auto">
                @forelse($ticket->replies()->public()->with('user')->orderBy('created_at')->get() as $reply)
                    <div class="mb-6 {{ $reply->is_from_customer ? 'text-right' : 'text-left' }}">
                        <div class="inline-block max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $reply->is_from_customer ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }}">
                            <div class="text-sm">
                                {{ $reply->message }}
                            </div>
                            @if($reply->attachments)
                                <div class="mt-2">
                                    @foreach($reply->attachments as $attachment)
                                        <a href="{{ route('tickets.download', ['path' => $attachment['path']]) }}" 
                                           class="text-xs {{ $reply->is_from_customer ? 'text-blue-100' : 'text-blue-600' }} hover:underline block">
                                            📎 {{ $attachment['name'] }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ $reply->user->name }} • {{ $reply->created_at->diffForHumans() }}
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center">No replies yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Reply Form -->
        @if(!in_array($ticket->status, ['resolved', 'closed']))
            <div class="bg-white rounded-lg shadow-md mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Add Reply</h3>
                </div>
                
                <form action="{{ route('tickets.reply', $ticket) }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                            Your Reply
                        </label>
                        <textarea id="message" 
                                  name="message" 
                                  rows="4"
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Type your reply here..."
                                  required>{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">
                            Attachments (optional)
                        </label>
                        <input type="file" 
                               id="attachments" 
                               name="attachments[]" 
                               multiple
                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-sm text-gray-500 mt-1">
                            Max 5MB per file. Allowed formats: jpg, png, pdf, doc, docx, txt
                        </p>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" 
                                class="px-6 py-2 bg-black text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Send Reply
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions</h3>
                
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('tickets.index') }}" 
                       class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                        ← Back to Tickets
                    </a>
                    
                    @if($ticket->status === 'resolved' && !$ticket->satisfaction_rating)
                        <button onclick="showRatingModal()" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Rate This Ticket
                        </button>
                    @endif
                    
                    @if(!in_array($ticket->status, ['resolved', 'closed']))
                        <form action="{{ route('tickets.close', $ticket) }}" 
                              method="POST" 
                              class="inline"
                              onsubmit="return confirm('Are you sure you want to close this ticket?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                Close Ticket
                            </button>
                        </form>
                    @elseif($ticket->status === 'resolved')
                        <form action="{{ route('tickets.reopen', $ticket) }}" 
                              method="POST" 
                              class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                                Reopen Ticket
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        @if($ticket->satisfaction_rating)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
                <h4 class="font-medium text-blue-800 mb-2">Your Feedback</h4>
                <div class="flex items-center mb-2">
                    <span class="text-sm text-blue-700 mr-2">Rating:</span>
                    @for($i = 1; $i <= 5; $i++)
                        <span class="text-lg {{ $i <= $ticket->satisfaction_rating ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                    @endfor
                </div>
                @if($ticket->customer_feedback)
                    <p class="text-sm text-blue-700">{{ $ticket->customer_feedback }}</p>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Rating Modal -->
@if($ticket->status === 'resolved' && !$ticket->satisfaction_rating)
<div id="ratingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Rate Your Experience</h3>
            
            <form action="{{ route('tickets.rate', $ticket) }}" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        How satisfied are you with the resolution?
                    </label>
                    <div class="flex justify-center space-x-2 mb-4">
                        @for($i = 1; $i <= 5; $i++)
                            <label class="cursor-pointer">
                                <input type="radio" name="satisfaction_rating" value="{{ $i }}" class="sr-only" onchange="updateStars({{ $i }})">
                                <span class="text-2xl text-gray-300 hover:text-yellow-400 star" data-rating="{{ $i }}">★</span>
                            </label>
                        @endfor
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="customer_feedback" class="block text-sm font-medium text-gray-700 mb-2">
                        Additional Comments (optional)
                    </label>
                    <textarea id="customer_feedback" 
                              name="customer_feedback" 
                              rows="3"
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Tell us about your experience..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="hideRatingModal()"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800">
                        Skip
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Submit Rating
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
function showRatingModal() {
    document.getElementById('ratingModal').classList.remove('hidden');
    document.getElementById('ratingModal').classList.add('flex');
}

function hideRatingModal() {
    document.getElementById('ratingModal').classList.add('hidden');
    document.getElementById('ratingModal').classList.remove('flex');
}

function updateStars(rating) {
    const stars = document.querySelectorAll('.star');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        }
    });
}
</script>
@endsection