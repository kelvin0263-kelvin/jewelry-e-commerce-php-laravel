@extends('layouts.app')

@section('title', 'Chat Conversation #' . $conversation->id)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center">
                            <a href="{{ route('chat-history.index') }}" 
                               class="text-gray-500 hover:text-gray-700 mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                            </a>
                            <h1 class="text-2xl font-bold text-gray-800">
                                Conversation #{{ $conversation->id }}
                            </h1>
                            <span class="ml-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($conversation->status === 'active') bg-green-100 text-green-800
                                @elseif($conversation->status === 'completed') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($conversation->status) }}
                            </span>
                        </div>
                        
                        <!-- Conversation Info -->
                        <div class="mt-3 flex flex-wrap items-center text-sm text-gray-600 space-x-6">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Agent: {{ $conversation->agent ? $conversation->agent->name : 'Unassigned' }}
                            </div>
                            
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Started: {{ $conversation->created_at->format('M j, Y g:i A') }}
                            </div>
                            
                            @if($conversation->ended_at)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Ended: {{ $conversation->ended_at->format('M j, Y g:i A') }}
                                </div>
                            @endif
                            
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                </svg>
                                {{ $conversation->messages->count() }} messages
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex space-x-2">
                        <a href="{{ route('chat-history.download', $conversation->id) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download Transcript
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Conversation Messages</h2>
                
                <div class="space-y-4 max-h-96 overflow-y-auto" id="messages-container">
                    @forelse($conversation->messages as $message)
                        @if($message->message_type === 'system')
                            <!-- System Message -->
                            <div class="flex justify-center">
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-2 max-w-md">
                                    <div class="text-center">
                                        <div class="text-xs font-medium text-yellow-800 mb-1">System Message</div>
                                        <div class="text-sm text-yellow-700">{{ $message->body }}</div>
                                        <div class="text-xs text-yellow-600 mt-1">{{ $message->created_at->format('M j, Y g:i A') }}</div>
                                    </div>
                                </div>
                            </div>
                        @else
                            @php
                                $isCustomer = $message->user && $message->user->id === auth()->id();
                            @endphp
                            
                            <!-- User Message -->
                            <div class="flex {{ $isCustomer ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-xs lg:max-w-md">
                                    <div class="flex items-end {{ $isCustomer ? 'flex-row-reverse' : 'flex-row' }}">
                                        <!-- Avatar -->
                                        <div class="flex-shrink-0 {{ $isCustomer ? 'ml-2' : 'mr-2' }}">
                                            <div class="w-8 h-8 rounded-full {{ $isCustomer ? 'bg-blue-500' : 'bg-gray-400' }} flex items-center justify-center">
                                                <span class="text-xs font-medium text-white">
                                                    {{ $message->user ? strtoupper(substr($message->user->name, 0, 1)) : '?' }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Message Bubble -->
                                        <div class="px-4 py-2 rounded-lg {{ $isCustomer ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }}">
                                            <div class="text-xs font-medium mb-1 {{ $isCustomer ? 'text-blue-100' : 'text-gray-600' }}">
                                                {{ $message->user ? $message->user->name : 'Unknown User' }}
                                                @if($message->user && $message->user->is_admin)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 ml-1">
                                                        Agent
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-sm">{{ $message->body }}</div>
                                            <div class="text-xs {{ $isCustomer ? 'text-blue-200' : 'text-gray-500' }} mt-1">
                                                {{ $message->created_at->format('g:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No messages</h3>
                            <p class="mt-1 text-sm text-gray-500">This conversation doesn't have any messages yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Conversation Summary (if completed) -->
        @if($conversation->status === 'completed' && $conversation->end_reason)
            <div class="bg-white rounded-lg shadow-md mt-6">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Conversation Summary</h2>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between text-sm">
                            <div>
                                <span class="font-medium text-gray-700">End Reason:</span>
                                <span class="ml-2 text-gray-900">{{ ucfirst($conversation->end_reason) }}</span>
                            </div>
                            @if($conversation->ended_at)
                                <div class="text-gray-500">
                                    Duration: {{ $conversation->created_at->diffForHumans($conversation->ended_at, true) }}
                                </div>
                            @endif
                        </div>
                        
                        @if($conversation->customer_feedback)
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <span class="font-medium text-gray-700">Customer Feedback:</span>
                                <p class="mt-1 text-gray-900">{{ $conversation->customer_feedback }}</p>
                            </div>
                        @endif
                        
                        @if($conversation->customer_rating)
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <span class="font-medium text-gray-700">Rating:</span>
                                <div class="flex items-center mt-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $conversation->customer_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                    <span class="ml-2 text-sm text-gray-600">({{ $conversation->customer_rating }}/5)</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-scroll to bottom of messages
    const messagesContainer = document.getElementById('messages-container');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
});
</script>
@endsection
