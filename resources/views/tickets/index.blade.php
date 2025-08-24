@extends('layouts.app')

@section('title', 'My Support Tickets')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">My Support Tickets</h1>
                    <p class="text-gray-600 mt-2">Track and manage your support requests</p>
                </div>
                <a href="{{ route('tickets.create') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-plus mr-2"></i>New Ticket
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tickets List -->
        <div class="bg-white rounded-lg shadow-md">
            @if($tickets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ticket
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Subject
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Priority
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Assigned To
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Created
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($tickets as $ticket)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $ticket->ticket_number }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $ticket->category_display }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <a href="{{ route('tickets.show', $ticket) }}" 
                                               class="hover:text-blue-600">
                                                {{ Str::limit($ticket->subject, 50) }}
                                            </a>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ Str::limit($ticket->description, 80) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $ticket->status_badge_class }} text-white">
                                            {{ ucwords(str_replace('_', ' ', $ticket->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $ticket->priority_badge_class }} text-white">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($ticket->assignedAgent)
                                            <div class="flex items-center">
                                                <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center mr-2">
                                                    <span class="text-xs font-medium text-gray-600">
                                                        {{ substr($ticket->assignedAgent->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                {{ $ticket->assignedAgent->name }}
                                            </div>
                                        @else
                                            <span class="text-gray-400">Unassigned</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div>{{ $ticket->created_at->format('M j, Y') }}</div>
                                        <div class="text-xs">{{ $ticket->created_at->format('g:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('tickets.show', $ticket) }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                View
                                            </a>
                                            @if(!in_array($ticket->status, ['resolved', 'closed']))
                                                <form action="{{ route('tickets.close', $ticket) }}" 
                                                      method="POST" 
                                                      class="inline"
                                                      onsubmit="return confirm('Are you sure you want to close this ticket?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900">
                                                        Close
                                                    </button>
                                                </form>
                                            @elseif($ticket->status === 'resolved')
                                                <form action="{{ route('tickets.reopen', $ticket) }}" 
                                                      method="POST" 
                                                      class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="text-green-600 hover:text-green-900">
                                                        Reopen
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($tickets->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $tickets->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No support tickets yet</h3>
                    <p class="mt-2 text-gray-500">Get started by creating your first support ticket.</p>
                    <div class="mt-6">
                        <a href="{{ route('tickets.create') }}" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Create Your First Ticket
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Help Section -->
        <div class="mt-6 bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Need Help?</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="bg-blue-100 rounded-lg p-4 mb-3">
                        <svg class="mx-auto h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.991 8.991 0 01-4.338-1.126L5 21l1.126-3.662A8.991 8.991 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
                        </svg>
                    </div>
                    <h4 class="font-medium text-gray-900">Live Chat</h4>
                    <p class="text-sm text-gray-600 mt-1">Chat with our support team in real-time</p>
                    <button onclick="openChatWidget()" 
                            class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Start Chat →
                    </button>
                </div>
                <div class="text-center">
                    <div class="bg-green-100 rounded-lg p-4 mb-3">
                        <svg class="mx-auto h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h4 class="font-medium text-gray-900">Knowledge Base</h4>
                    <p class="text-sm text-gray-600 mt-1">Browse our frequently asked questions</p>
                    <a href="{{ route('faq.index') }}" 
                       class="mt-2 text-green-600 hover:text-green-800 text-sm font-medium">
                        View FAQ →
                    </a>
                </div>
                <div class="text-center">
                    <div class="bg-purple-100 rounded-lg p-4 mb-3">
                        <svg class="mx-auto h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h4 class="font-medium text-gray-900">Email Support</h4>
                    <p class="text-sm text-gray-600 mt-1">Send us an email for detailed inquiries</p>
                    <a href="mailto:support@example.com" 
                       class="mt-2 text-purple-600 hover:text-purple-800 text-sm font-medium">
                        Email Us →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openChatWidget() {
    // Open the chat widget
    if (typeof window.startLiveChat === 'function') {
        window.startLiveChat();
    } else {
        alert('Chat widget not available at the moment. Please create a ticket for support.');
    }
}
</script>
@endsection