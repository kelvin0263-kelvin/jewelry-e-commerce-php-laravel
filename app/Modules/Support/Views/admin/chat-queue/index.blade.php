@extends('layouts.admin')

@section('title', 'Chat Queue Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Chat Queue Management</h1>
        <p class="text-gray-600 mt-2">Manage incoming chat requests and agent assignments</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Waiting Customers</div>
                    <div class="text-2xl font-bold text-gray-900" id="waiting-count">{{ $stats['waiting_customers'] }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Active Chats</div>
                    <div class="text-2xl font-bold text-gray-900" id="active-count">{{ $stats['active_chats'] }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Available Agents</div>
                    <div class="text-2xl font-bold text-gray-900" id="available-agents">{{ $stats['available_agents'] }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Avg Wait Time</div>
                    <div class="text-2xl font-bold text-gray-900" id="avg-wait">{{ round($stats['average_wait_time'] / 60, 1) }}m</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Agent Status Panel -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Agent Status</h3>
                    <p class="text-sm text-gray-600">Manage your availability</p>
                </div>
                <div class="p-4">
                    <!-- Status Controls -->
                    <div class="space-y-2 mb-4">
                        <button onclick="updateMyStatus('online')" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Online
                        </button>
                        <button onclick="updateMyStatus('away')" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded-md transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Away
                        </button>
                        <button onclick="fixAgentStatus()" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Fix Status
                        </button>
                    </div>

                    <!-- Agents List -->
                    <div class="space-y-3" id="agents-list">
                        @foreach($agents as $agent)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $agent->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $agent->current_active_chats }}/{{ $agent->max_concurrent_chats }} chats</div>
                                    </div>
                                </div>
                                <div>
                                    @if($agent->status === 'online')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-green-600 bg-green-100">
                                            <span class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></span>
                                            Online
                                        </span>
                                    @elseif($agent->status === 'away')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-yellow-600 bg-yellow-100">
                                            <span class="w-1.5 h-1.5 bg-yellow-400 rounded-full mr-1"></span>
                                            Away
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-gray-600 bg-gray-100">
                                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></span>
                                            Offline
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Queue -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Pending Chats Queue (FIFO)</h3>
                            <p class="text-sm text-gray-600">First in, first out - customers waiting for assistance</p>
                        </div>
                        <button onclick="refreshQueue()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Refresh
                        </button>
                    </div>
                </div>
                <div class="p-4">
                    <div id="pending-queue">
                        @if($pendingChats->isEmpty())
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V10a2 2 0 012-2h2m2 4h6m-6 4h6m-6-8h6m-6 4h6"></path>
                                </svg>
                                <p class="text-lg font-medium text-gray-900 mb-1">No customers waiting in queue</p>
                                <p class="text-gray-500">New chat requests will appear here</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($pendingChats as $chat)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:border-gray-300 transition-colors" data-queue-id="{{ $chat->id }}">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center flex-1 min-w-0">
                                                <div class="flex-shrink-0 mr-4">
                                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium text-white bg-blue-500">
                                                        #{{ $chat->position }}
                                                    </span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center">
                                                        <h4 class="font-medium text-gray-900 truncate">{{ $chat->customer->name }}</h4>
                                                        @if($chat->priority !== 'normal')
                                                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $chat->priority === 'urgent' ? 'text-red-600 bg-red-100' : 'text-yellow-600 bg-yellow-100' }}">
                                                                {{ ucfirst($chat->priority) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p class="text-sm text-gray-500 truncate">{{ $chat->customer->email }}</p>
                                                    <div class="flex items-center mt-1 text-xs text-gray-400">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Waiting {{ $chat->wait_time }} minutes
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center space-x-2 ml-4">
                                                <button onclick="acceptChat({{ $chat->id }})" 
                                                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md text-sm transition-colors">
                                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Accept
                                                </button>
                                                <div class="relative">
                                                    <button class="bg-gray-100 hover:bg-gray-200 text-gray-600 p-1 rounded-md transition-colors" onclick="toggleDropdown({{ $chat->id }})">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                                        </svg>
                                                    </button>
                                                    <div id="dropdown-{{ $chat->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-10">
                                                        <div class="py-1">
                                                            <button onclick="assignToAgent({{ $chat->id }})" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                                Assign to Agent
                                                            </button>
                                                            <button onclick="abandonChat({{ $chat->id }})" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 w-full text-left">
                                                                Remove from Queue
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if($chat->escalation_context || $chat->initial_message)
                                            <div class="mt-3 pt-3 border-t border-gray-100">
                                                @if($chat->escalation_context)
                                                    <div class="flex items-center text-xs text-blue-600 mb-1">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                                        </svg>
                                                        Escalated from: {{ $chat->escalation_context['issue_category'] ?? 'Self-service' }}
                                                    </div>
                                                @endif
                                                @if($chat->initial_message)
                                                    <p class="text-sm text-gray-600 italic">
                                                        "{{ Str::limit($chat->initial_message, 80) }}"
                                                    </p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Agent Modal -->
<div id="assignAgentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Assign Chat to Agent</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="assignAgentForm">
                <input type="hidden" id="assignQueueId" name="queue_id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Agent</label>
                    <select class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="assignAgentId" name="agent_id" required>
                        <option value="">Choose an agent...</option>
                        @foreach($agents->where('status', 'online') as $agent)
                            @if($agent->canAcceptChats())
                                <option value="{{ $agent->user_id }}">
                                    {{ $agent->user->name }} ({{ $agent->current_active_chats }}/{{ $agent->max_concurrent_chats }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </form>
            <div class="flex justify-end space-x-2">
                <button onclick="closeModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md transition-colors">
                    Cancel
                </button>
                <button onclick="submitAssignment()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition-colors">
                    Assign
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentQueueId = null;

// Auto-refresh every 30 seconds
setInterval(refreshQueue, 5000);

function toggleDropdown(chatId) {
    const dropdown = document.getElementById(`dropdown-${chatId}`);
    dropdown.classList.toggle('hidden');
    
    // Close other dropdowns
    document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
        if (el.id !== `dropdown-${chatId}`) {
            el.classList.add('hidden');
        }
    });
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick^="toggleDropdown"]')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
            el.classList.add('hidden');
        });
    }
});

// button trigger (ui update)
function closeModal() {
    document.getElementById('assignAgentModal').classList.add('hidden');
}


//done button trigger
function refreshQueue() {
    fetch('/admin/chat-queue/data')
        .then(response => response.json())
        .then(data => {
            updateStats(data.stats);
            updatePendingQueue(data.pending_chats);
            updateAgentsList(data.agents);
        })
        .catch(error => console.error('Error refreshing queue:', error));
}

function updateStats(stats) {
    document.getElementById('waiting-count').textContent = stats.waiting_customers;
    document.getElementById('active-count').textContent = stats.active_chats;
    document.getElementById('available-agents').textContent = stats.available_agents;
    document.getElementById('avg-wait').textContent = Math.round(stats.average_wait_time / 60 * 10) / 10 + 'm';
}

function updatePendingQueue(chats) {
    const container = document.getElementById('pending-queue');
    if (chats.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V10a2 2 0 012-2h2m2 4h6m-6 4h6m-6-8h6m-6 4h6"></path>
                </svg>
                <p class="text-lg font-medium text-gray-900 mb-1">No customers waiting in queue</p>
                <p class="text-gray-500">New chat requests will appear here</p>
            </div>
        `;
        return;
    }else{
    
    // Update queue display (simplified for demo)
    // In production, you'd want to update each item individually
     // Build the complete queue list HTML
    let queueHTML = '<div class="space-y-4">';
    
    chats.forEach(chat => {
        const priorityClass = chat.priority === 'urgent' ? 'text-red-600 bg-red-100' : 
                             chat.priority === 'high' ? 'text-yellow-600 bg-yellow-100' : '';
        
        queueHTML += `
            <div class="border border-gray-200 rounded-lg p-4 hover:border-gray-300 transition-colors" data-queue-id="${chat.id}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center flex-1 min-w-0">
                        <div class="flex-shrink-0 mr-4">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium text-white bg-blue-500">
                                #${chat.position}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center">
                                <h4 class="font-medium text-gray-900 truncate">${chat.customer.name}</h4>
                                ${chat.priority !== 'normal' ? `
                                    <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${priorityClass}">
                                        ${chat.priority.charAt(0).toUpperCase() + chat.priority.slice(1)}
                                    </span>
                                ` : ''}
                            </div>
                            <p class="text-sm text-gray-500 truncate">${chat.customer.email}</p>
                            <div class="flex items-center mt-1 text-xs text-gray-400">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Waiting ${chat.wait_time} minutes
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2 ml-4">
                        <button onclick="acceptChat(${chat.id})" 
                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md text-sm transition-colors">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Accept
                        </button>
                        <div class="relative">
                            <button class="bg-gray-100 hover:bg-gray-200 text-gray-600 p-1 rounded-md transition-colors" onclick="toggleDropdown(${chat.id})">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                </svg>
                            </button>
                            <div id="dropdown-${chat.id}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-10">
                                <div class="py-1">
                                    <button onclick="assignToAgent(${chat.id})" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                        Assign to Agent
                                    </button>
                                    <button onclick="abandonChat(${chat.id})" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 w-full text-left">
                                        Remove from Queue
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                ${chat.escalation_context || chat.initial_message ? `
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        ${chat.escalation_context ? `
                            <div class="flex items-center text-xs text-blue-600 mb-1">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                </svg>
                                Escalated from: ${chat.escalation_context.issue_category || 'Self-service'}
                            </div>
                        ` : ''}
                        ${chat.initial_message ? `
                            <div class="text-xs text-gray-600">
                                <strong>Initial message:</strong> ${chat.initial_message.substring(0, 100)}${chat.initial_message.length > 100 ? '...' : ''}
                            </div>
                        ` : ''}
                    </div>
                ` : ''}
            </div>
        `;
    });
    
    queueHTML += '</div>';
    container.innerHTML = queueHTML;
}
}

window.refreshQueue = refreshQueue;

function updateAgentsList(agents) {
    const agentsList = document.getElementById('agents-list');
    if (!agentsList) return;
    
    agentsList.innerHTML = '';
    
    agents.forEach(agent => {
        const agentElement = document.createElement('div');
        agentElement.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';
        
        // Determine status badge classes and text
        let statusBadgeClass = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-gray-600 bg-gray-100';
        let statusText = 'Offline';
        let statusDotClass = 'w-1.5 h-1.5 bg-gray-400 rounded-full mr-1';
        
        if (agent.status === 'online') {
            statusBadgeClass = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-green-600 bg-green-100';
            statusText = 'Online';
            statusDotClass = 'w-1.5 h-1.5 bg-green-400 rounded-full mr-1';
        } else if (agent.status === 'away') {
            statusBadgeClass = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-yellow-600 bg-yellow-100';
            statusText = 'Away';
            statusDotClass = 'w-1.5 h-1.5 bg-yellow-400 rounded-full mr-1';
        }
        
        agentElement.innerHTML = `
            <div class="flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <div class="font-medium text-gray-900">${agent.user.name}</div>
                    <div class="text-xs text-gray-500">${agent.current_active_chats}/${agent.max_concurrent_chats} chats</div>
                </div>
            </div>
            <div>
                <span class="${statusBadgeClass}">
                    <span class="${statusDotClass}"></span>
                    ${statusText}
                </span>
            </div>
        `;
        
        agentsList.appendChild(agentElement);
    });
}

//done button trigger
function acceptChat(queueId) {
    if (confirm('Accept this chat?')) {
        fetch(`/admin/chat-queue/${queueId}/accept`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Chat accepted! Redirecting to conversation...');
                window.location.href = `/admin/chat#conversation-${data.conversation_id}`;
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to accept chat');
        });
    }
}

//done button trigger (just show ui only)
function assignToAgent(queueId) {
    currentQueueId = queueId;
    document.getElementById('assignQueueId').value = queueId;
    document.getElementById('assignAgentModal').classList.remove('hidden');
}


//done button trigger
function submitAssignment() {
    const agentId = document.getElementById('assignAgentId').value;
    if (!agentId) {
        alert('Please select an agent');
        return;
    }

    fetch(`/admin/chat-queue/${currentQueueId}/assign`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ agent_id: agentId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Chat assigned successfully!');
            closeModal();
            refreshQueue();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to assign chat');
    });
}

//done button trigger
function abandonChat(queueId) {
    if (confirm('Remove this customer from queue?')) {
        fetch(`/admin/chat-queue/${queueId}/abandon`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Customer removed from queue');
                refreshQueue();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to remove from queue');
        });
    }
}


//done button trigger
function updateMyStatus(status) {
    // Immediately update UI to show loading state
    const statusButtons = document.querySelectorAll('[onclick^="updateMyStatus"]');
    statusButtons.forEach(btn => {
        btn.disabled = true;
        btn.classList.add('opacity-50');
    });
    
    fetch('/admin/chat-queue/agent-status', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ 
            status: status,
            accepting_chats: status === 'online'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message briefly
            showStatusMessage(`Status updated to ${status.toUpperCase()}`, 'success');
            // Refresh the queue to update agent list
            refreshQueue();
        } else {
            showStatusMessage('Failed to update status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showStatusMessage('Failed to update status', 'error');
    })
    .finally(() => {
        // Re-enable buttons
        statusButtons.forEach(btn => {
            btn.disabled = false;
            btn.classList.remove('opacity-50');
        });
    });
}

function fixAgentStatus() {
    // Disable fix button while processing
    const fixBtn = document.querySelector('[onclick="fixAgentStatus()"]');
    if (fixBtn) {
        fixBtn.disabled = true;
        fixBtn.classList.add('opacity-50');
        fixBtn.textContent = 'Fixing...';
    }
    
    fetch('/admin/chat-queue/agent-status', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ 
            status: 'online',
            accepting_chats: true,
            current_active_chats: 0,
            force_reset: true
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showStatusMessage('✅ Agent status fixed! You can now accept chats.', 'success');
            refreshQueue();
        } else {
            showStatusMessage('❌ Failed to fix status: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showStatusMessage('❌ Failed to fix status', 'error');
    })
    .finally(() => {
        // Re-enable button
        if (fixBtn) {
            fixBtn.disabled = false;
            fixBtn.classList.remove('opacity-50');
            fixBtn.textContent = 'Fix Status';
        }
    });
}

// Helper function to show status messages instead of alerts
function showStatusMessage(message, type = 'info') {
    // Remove any existing status messages
    const existingMessage = document.getElementById('status-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Create new status message
    const messageDiv = document.createElement('div');
    messageDiv.id = 'status-message';
    messageDiv.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-white font-medium transition-opacity duration-300 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    messageDiv.textContent = message;
    
    document.body.appendChild(messageDiv);
    
    // Auto-hide after 3 seconds
    setTimeout(() => {
        messageDiv.style.opacity = '0';
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 300);
    }, 3000);
}
</script>
@endsection

