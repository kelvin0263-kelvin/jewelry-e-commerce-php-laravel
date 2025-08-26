@extends('layouts.app')

@section('title', 'Chat History')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Chat History</h1>
                        <p class="text-gray-600 mt-2">View your previous conversations with our support team</p>
                    </div>
                    
                    <!-- Search Form -->
                    <div class="mt-4 md:mt-0">
                        <form action="{{ route('chat-history.search') }}" method="GET" class="flex">
                            <input type="text" 
                                   name="q" 
                                   value="{{ $query ?? '' }}" 
                                   placeholder="Search conversations..." 
                                   class="px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-500 text-white rounded-r-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Search
                            </button>
                        </form>
                    </div>
                </div>
                
                @if(isset($query) && !empty($query))
                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                        <p class="text-blue-800">
                            Search results for: <strong>"{{ $query }}"</strong>
                            <a href="{{ route('chat-history.index') }}" class="ml-2 text-blue-600 hover:text-blue-800 underline">Clear search</a>
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Conversations List -->
        <div class="bg-white rounded-lg shadow-md">
            @if($conversations->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($conversations as $conversation)
                        <div class="p-6 hover:bg-gray-50 transition-colors duration-150">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <!-- Conversation Header -->
                                    <div class="flex items-center mb-2">
                                        <h3 class="text-lg font-semibold text-gray-800">
                                            Conversation #{{ $conversation->id }}
                                        </h3>
                                        <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($conversation->status === 'active') bg-green-100 text-green-800
                                            @elseif($conversation->status === 'completed') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($conversation->status) }}
                                        </span>
                                    </div>
                                    
                                    <!-- Agent Info -->
                                    <div class="flex items-center text-sm text-gray-600 mb-2">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Agent: {{ $conversation->agent ? $conversation->agent->name : 'Unassigned' }}
                                    </div>
                                    
                                    <!-- Last Message Preview -->
                                    @if($conversation->messages->count() > 0)
                                        <div class="text-sm text-gray-700 mb-3">
                                            <span class="font-medium">Last message:</span>
                                            {{ Str::limit($conversation->messages->first()->body, 100) }}
                                        </div>
                                    @endif
                                    
                                    <!-- Timestamps -->
                                    <div class="flex items-center text-xs text-gray-500 space-x-4">
                                        <span>
                                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Started: {{ $conversation->created_at->format('M j, Y g:i A') }}
                                        </span>
                                        @if($conversation->ended_at)
                                            <span>
                                                Ended: {{ $conversation->ended_at->format('M j, Y g:i A') }}
                                            </span>
                                        @endif
                                        <span>
                                            Updated: {{ $conversation->updated_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex flex-col space-y-2 ml-4">
                                    @if($conversation->status === 'active')
                                        <button onclick="continueChat({{ $conversation->id }})" 
                                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                            Continue Chat
                                        </button>
                                    @endif
                                    
                                    <a href="{{ route('chat-history.show', $conversation->id) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                        View Chat
                                    </a>
                                    
                                    <a href="{{ route('chat-history.download', $conversation->id) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $conversations->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No chat history</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(isset($query))
                            No conversations found matching your search.
                        @else
                            You haven't had any chat conversations yet.
                        @endif
                    </p>
                    <div class="mt-6">
                        @if(isset($query))
                            <a href="{{ route('chat-history.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                View all conversations
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Start a new chat
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function continueChat(conversationId) {
    // Set the conversation ID for the widget
    window.conversationId = conversationId;
    
    // Trigger the chat widget to open with the existing conversation
    if (typeof window.startLiveChat === 'function') {
        // Use the existing startLiveChat function but with a specific conversation
        window.startLiveChat(conversationId);
    } else {
        // Fallback: redirect to homepage and open chat
        window.location.href = '/?open_chat=' + conversationId;
    }
}
</script>
@endsection
