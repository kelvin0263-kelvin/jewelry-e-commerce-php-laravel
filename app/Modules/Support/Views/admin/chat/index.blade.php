@extends('layouts.admin')

@section('title', 'Chat Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Chat Management</h1>
        <p class="text-gray-600 mt-2">Manage customer conversations and messages</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Total Conversations</div>
                    <div class="text-2xl font-bold text-gray-900" id="total-conversations">0</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Active Chats</div>
                    <div class="text-2xl font-bold text-gray-900" id="active-chats">0</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Pending</div>
                    <div class="text-2xl font-bold text-gray-900" id="pending-chats">0</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center">
                <div class="p-2 bg-gray-100 rounded-lg">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Completed</div>
                    <div class="text-2xl font-bold text-gray-900" id="completed-chats">0</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Chat Interface -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="flex h-96">
            <!-- Conversations List -->
            <div class="w-1/3 border-r border-gray-200">
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <h2 class="font-semibold text-gray-700">Conversations</h2>
                        <button onclick="loadConversations()" class="text-blue-600 hover:text-blue-800 text-sm">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Refresh
                        </button>
                    </div>
                </div>
                <div class="overflow-y-auto" style="height: calc(100% - 60px);" id="conversations-list">
                    <div class="p-4 text-center text-gray-500">
                        Loading conversations...
                    </div>
                </div>
            </div>

            <!-- Messages Display -->
            <div class="w-2/3 flex flex-col">
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="font-semibold text-gray-700" id="chat-header">Select a conversation</h2>
                </div>
                <div class="flex-1 overflow-y-auto p-4 bg-gray-50" id="messages-container">
                    <div class="text-center text-gray-500 mt-20">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <p class="text-lg font-medium text-gray-900">Select a conversation</p>
                        <p class="text-gray-500">Choose a conversation from the list to view messages</p>
                    </div>
                </div>
                <!-- Admin Reply Form -->
                <div class="p-4 border-t border-gray-200 bg-white" id="reply-form-container" style="display: none;">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600" id="conversation-status" style="display: none;"></span>
                        <button type="button" id="terminate-conversation-btn" onclick="terminateConversation()" 
                                class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-sm transition-colors" style="display: none;">
                            End Chat
                        </button>
                    </div>
                    <form id="admin-reply-form" class="flex gap-2">
                        <input type="hidden" id="current-conversation-id" value="">
                        <input type="text" id="admin-message-input" placeholder="Type your reply..." 
                               class="flex-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" disabled>
                        <button type="submit" id="send-message-btn" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors" style="display: none;">
                            Send
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentConversationId = null;
    let conversationState = {}; // Track conversation states

    // Function to update conversation state
    function updateConversationState(conversationId, state) {
        conversationState[conversationId] = state;
        console.log('🔄 Updated conversation state for', conversationId, ':', state);
    }

    // Function to get conversation state
    function getConversationState(conversationId) {
        return conversationState[conversationId] || { status: 'unknown', terminated: false };
    }

    // Update statistics cards
    function updateStatistics(conversations) {
        const totalConversations = conversations.length;
        const activeChats = conversations.filter(c => c.status === 'active').length;
        const pendingChats = conversations.filter(c => c.status === 'pending').length;
        const completedChats = conversations.filter(c => c.status === 'completed' || c.status === 'abandoned').length;

        document.getElementById('total-conversations').textContent = totalConversations;
        document.getElementById('active-chats').textContent = activeChats;
        document.getElementById('pending-chats').textContent = pendingChats;
        document.getElementById('completed-chats').textContent = completedChats;
    }



    // Load conversations on page load
    loadConversations();

    // Load all conversations
    async function loadConversations() {
        try {
            console.log('Attempting to fetch conversations from: /admin/chat/conversations');
            const response = await fetch('/admin/chat/conversations');
            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const conversations = await response.json();
            console.log('Fetched conversations:', conversations);
            
            // Update statistics cards
            updateStatistics(conversations);
            
            const conversationsList = document.getElementById('conversations-list');
            
            if (conversations.length === 0) {
                conversationsList.innerHTML = `
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <p class="font-medium text-gray-900 mb-1">No conversations yet</p>
                        <p class="text-sm text-gray-500">Customer conversations will appear here</p>
                    </div>
                `;
                return;
            }

            conversationsList.innerHTML = '';
            conversations.forEach(conversation => {
                const conversationElement = document.createElement('div');
                
                console.log('🔍 Conversation', conversation.id, 'status:', conversation.status, 'ended_at:', conversation.ended_at);
                
                // Update local conversation state
                updateConversationState(conversation.id, {
                    status: conversation.status,
                    terminated: conversation.status === 'completed' || conversation.status === 'abandoned' || conversation.ended_at,
                    ended_at: conversation.ended_at
                });
                
                // Add different styling based on conversation status
                let statusClass = 'p-4 border-b border-gray-100 cursor-pointer hover:bg-blue-50 transition-colors duration-200';
                let statusColor = 'text-green-600';
                let statusText = 'Active';
                let statusBgColor = 'bg-green-100';
                
                if (conversation.status === 'completed' || conversation.ended_at) {
                    statusClass = 'p-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50 transition-colors duration-200';
                    statusColor = 'text-gray-600';
                    statusText = 'Completed';
                    statusBgColor = 'bg-gray-100';
                } else if (conversation.status === 'abandoned') {
                    statusClass = 'p-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50 transition-colors duration-200';
                    statusColor = 'text-gray-600';
                    statusText = 'Abandoned';
                    statusBgColor = 'bg-gray-100';
                } else if (conversation.status === 'active' && !conversation.assigned_agent_id) {
                    statusColor = 'text-yellow-600';
                    statusText = 'Waiting';
                    statusBgColor = 'bg-yellow-100';
                } else if (conversation.status === 'pending') {
                    statusColor = 'text-blue-600';
                    statusText = 'Pending';
                    statusBgColor = 'bg-blue-100';
                }
                
                conversationElement.className = statusClass;
                conversationElement.innerHTML = `
                    <div class="flex justify-between items-start">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center mb-1">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 truncate">${conversation.user.name}</div>
                                    <div class="text-sm text-gray-500 truncate">${conversation.user.email}</div>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="text-xs text-gray-400">#${conversation.id}</div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${statusColor} ${statusBgColor}">
                                    ${statusText}
                                </span>
                            </div>
                        </div>
                    </div>
                `;
                conversationElement.addEventListener('click', () => loadMessages(conversation.id, conversation.user.name));
                conversationsList.appendChild(conversationElement);
            });
            
            // Auto-subscribe admin to all conversation channels for real-time notifications
            subscribeToAllConversations(conversations);
        } catch (error) {
            console.error('Failed to load conversations:', error);
            console.error('Error details:', error.message);
            document.getElementById('conversations-list').innerHTML = `
                <div class="p-4 text-center text-red-500">
                    <div>Failed to load conversations</div>
                    <div class="text-sm mt-2">${error.message}</div>
                    <button onclick="loadConversations()" class="mt-2 bg-blue-500 text-white px-3 py-1 rounded text-sm">
                        Retry
                    </button>
                </div>
            `;
        }
    }

    // Load messages for a specific conversation
    async function loadMessages(conversationId, userName) {
        currentConversationId = conversationId;
        
        try {
            // Fetch conversation details first
            const conversationResponse = await fetch(`/admin/chat/conversations/${conversationId}`);
            const conversation = await conversationResponse.json();
            
            const response = await fetch(`/admin/chat/conversations/${conversationId}/messages`);
            const messages = await response.json();
            
            // Update header
            document.getElementById('chat-header').textContent = `Chat with ${userName}`;
            
            // Show reply form
            document.getElementById('current-conversation-id').value = conversationId;
            const replyFormContainer = document.getElementById('reply-form-container');
            replyFormContainer.style.display = 'block';
            
            console.log('🔍 Loading conversation status:', conversation.status, 'ended_at:', conversation.ended_at);
            console.log('🔍 Full conversation object:', conversation);
            
            // Update conversation state immediately
            updateConversationState(conversationId, {
                status: conversation.status,
                terminated: conversation.status === 'completed' || conversation.status === 'abandoned' || conversation.ended_at,
                ended_at: conversation.ended_at
            });
            
            // Initialize UI elements and IMMEDIATELY reset them to prevent flash of wrong state
            const statusElement = document.getElementById('conversation-status');
            const messageInput = document.getElementById('admin-message-input');
            const sendButton = document.getElementById('send-message-btn');
            const terminateBtn = document.getElementById('terminate-conversation-btn');
            
            // IMMEDIATELY clear default states to prevent flash
            if (statusElement) statusElement.textContent = '';
            if (terminateBtn) terminateBtn.style.display = 'none';
            if (sendButton) sendButton.style.display = 'none';
            if (messageInput) {
                messageInput.disabled = true;
                messageInput.value = '';
            }
            
            // Force clear any existing termination notice first
            const existingNotice = document.getElementById('termination-notice');
            if (existingNotice) {
                existingNotice.remove();
            }
            
            // Check if conversation is terminated - be more explicit about the conditions
            const isCompleted = conversation.status === 'completed';
            const isAbandoned = conversation.status === 'abandoned';
            const hasEndedAt = conversation.ended_at && conversation.ended_at !== null;
            
            // Also check messages for system termination messages
            const hasTerminationMessage = messages.some(msg => 
                msg.message_type === 'system' && 
                (msg.body.includes('ended by customer') || msg.body.includes('ended by agent') || msg.body.includes('Conversation ended'))
            );
            
            const isTerminated = isCompleted || isAbandoned || hasEndedAt || hasTerminationMessage;
            
            console.log('🔍 Termination check:', { isCompleted, isAbandoned, hasEndedAt, hasTerminationMessage, isTerminated });
            
            if (isTerminated) {
                console.log('⚠️ Conversation is terminated, setting up terminated UI');
                
                // FORCE HIDE everything immediately
                if (statusElement) {
                    statusElement.style.display = 'none';
                    console.log('✅ Status element hidden');
                }
                
                if (messageInput) {
                    messageInput.disabled = true;
                    messageInput.placeholder = `Chat has been terminated`;
                    messageInput.style.backgroundColor = '#f3f4f6';
                    messageInput.style.cursor = 'not-allowed';
                    messageInput.value = '';
                    messageInput.style.display = 'none';
                    console.log('✅ Message input hidden');
                }
                if (sendButton) {
                    sendButton.disabled = true;
                    sendButton.style.display = 'none';
                    sendButton.style.visibility = 'hidden';
                    console.log('✅ Send button hidden');
                }
                if (terminateBtn) {
                    terminateBtn.style.display = 'none';
                    terminateBtn.disabled = true;
                    console.log('✅ Terminate button hidden');
                }
                
                // Add a prominent termination notice
                const terminationNotice = document.createElement('div');
                terminationNotice.id = 'termination-notice';
                terminationNotice.className = 'bg-red-50 border border-red-200 rounded-lg p-4 mb-4 text-center';
                
                const terminatedBy = conversation.end_reason === 'resolved' ? 'admin' : 'customer';
                const statusText = conversation.status === 'completed' ? 'Completed' : 'Abandoned';
                
                terminationNotice.innerHTML = `
                    <div class="text-red-800 font-medium text-lg">
                        🚫 Chat ${statusText}
                    </div>
                    <div class="text-red-600 text-sm mt-1">
                        This conversation was ended ${conversation.end_reason ? `(${conversation.end_reason})` : ''} 
                        ${conversation.ended_at ? `on ${new Date(conversation.ended_at).toLocaleString()}` : ''}
                    </div>
                `;
                replyFormContainer.insertBefore(terminationNotice, replyFormContainer.firstChild);
                
                console.log('✅ Terminated UI setup complete');
            } else {
                // Enable form for active conversations
                console.log('✅ Conversation is active, setting up active UI');
                
                if (statusElement) {
                    // Don't show "Waiting for Agent" - keep it hidden
                    statusElement.style.display = 'none';
                    console.log('✅ Status element kept hidden');
                }
                
                if (messageInput) {
                    messageInput.disabled = false;
                    messageInput.placeholder = 'Type your reply...';
                    messageInput.style.backgroundColor = '';
                    messageInput.style.cursor = '';
                    messageInput.style.display = 'block';
                    console.log('✅ Message input enabled');
                }
                if (sendButton) {
                    sendButton.disabled = false;
                    sendButton.style.display = 'inline-flex';
                    sendButton.style.visibility = 'visible';
                    sendButton.textContent = 'Send';
                    console.log('✅ Send button shown');
                }
                if (terminateBtn) {
                    terminateBtn.style.display = 'inline-flex';
                    terminateBtn.disabled = false;
                    terminateBtn.textContent = 'End Chat';
                    console.log('✅ Terminate button shown');
                }
                
                console.log('✅ Active UI setup complete');
            }
            
            // Display messages
            const messagesContainer = document.getElementById('messages-container');
            messagesContainer.innerHTML = '';
            
            if (messages.length === 0) {
                messagesContainer.innerHTML = '<div class="text-center text-gray-500">No messages yet</div>';
            } else {
                messages.forEach(message => {
                    addMessageToContainer(message);
                });
            }
            
            // After displaying messages, do another termination check
            const systemMessages = messages.filter(msg => msg.message_type === 'system');
            const hasTerminationSystemMessage = systemMessages.some(msg => 
                msg.body.includes('ended by customer') || 
                msg.body.includes('ended by agent') || 
                msg.body.includes('Conversation ended')
            );
            
            console.log('🔍 System messages found:', systemMessages.length);
            console.log('🔍 Has termination system message:', hasTerminationSystemMessage);
            
            // If we find termination messages but UI isn't terminated, force termination
            if (hasTerminationSystemMessage && !isTerminated) {
                console.log('🚨 FORCE TERMINATION: Found system termination message but UI not terminated');
                
                // Force hide UI elements
                const statusEl = document.getElementById('conversation-status');
                const messageInp = document.getElementById('admin-message-input');
                const sendBtn = document.getElementById('send-message-btn');
                const termBtn = document.getElementById('terminate-conversation-btn');
                
                if (statusEl) statusEl.style.display = 'none';
                if (messageInp) {
                    messageInp.style.display = 'none';
                    messageInp.disabled = true;
                }
                if (sendBtn) {
                    sendBtn.style.display = 'none';
                    sendBtn.disabled = true;
                }
                if (termBtn) {
                    termBtn.style.display = 'none';
                    termBtn.disabled = true;
                }
                
                // Add termination notice if not already present
                const replyContainer = document.getElementById('reply-form-container');
                if (replyContainer && !document.getElementById('termination-notice')) {
                    const notice = document.createElement('div');
                    notice.id = 'termination-notice';
                    notice.className = 'bg-red-50 border border-red-200 rounded-lg p-4 mb-4 text-center';
                    notice.innerHTML = `
                        <div class="text-red-800 font-medium text-lg">
                            🚫 Chat Terminated by Customer
                        </div>
                        <div class="text-red-600 text-sm mt-1">
                            The customer has ended this conversation.
                        </div>
                    `;
                    replyContainer.insertBefore(notice, replyContainer.firstChild);
                }
            }
            
            // Scroll to bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            
            // Listen for real-time messages
            listenForRealTimeMessages(conversationId);
            
            // FORCE another check after a small delay to ensure UI state is correct
            setTimeout(() => {
                console.log('🔄 Force checking UI state after delay');
                const statusEl = document.getElementById('conversation-status');
                const termBtn = document.getElementById('terminate-conversation-btn');
                const sendBtn = document.querySelector('#admin-reply-form button[type="submit"]');
                
                if (isTerminated) {
                    console.log('🔄 Force ensuring terminated UI state');
                    if (statusEl) {
                        statusEl.style.display = 'none';
                        console.log('❌ Status was visible! Force hiding...');
                    }
                    if (termBtn && termBtn.style.display !== 'none') {
                        console.log('❌ Terminate button was shown! Force hiding...');
                        termBtn.style.display = 'none';
                        termBtn.disabled = true;
                    }
                    if (sendBtn && sendBtn.style.display !== 'none') {
                        console.log('❌ Send button was shown! Force hiding...');
                        sendBtn.style.display = 'none';
                        sendBtn.style.visibility = 'hidden';
                        sendBtn.disabled = true;
                    }
                    const messageInp = document.getElementById('admin-message-input');
                    if (messageInp && messageInp.style.display !== 'none') {
                        console.log('❌ Message input was visible! Force hiding...');
                        messageInp.style.display = 'none';
                        messageInp.disabled = true;
                    }
                } else {
                    console.log('🔄 Conversation is active, UI should show active state');
                }
                console.log('🔄 Force check complete');
            }, 100);
        } catch (error) {
            console.error('Failed to load messages:', error);
            document.getElementById('messages-container').innerHTML = '<div class="text-center text-red-500">Failed to load messages</div>';
        }
    }

    // Subscribe admin to all conversation channels for real-time notifications
    function subscribeToAllConversations(conversations) {
        if (!window.Echo) {
            console.error('❌ Laravel Echo is not initialized');
            return;
        }
        
        console.log('📡 Subscribing to conversations:', conversations.length);
        console.log('🔧 Echo connector status:', window.Echo.connector.pusher.connection.state);
        
        conversations.forEach(conversation => {
            const channelName = 'conversation.' + conversation.id;
            console.log('Subscribing to channel:', channelName);
            
            try {
                const channel = window.Echo.private(channelName);
                console.log('📺 Created channel for', channelName, ':', channel);
                
                // Listen for subscription success/failure
                channel.subscribed(() => {
                    console.log('✅ Successfully subscribed to', channelName);
                });
                
                channel.error((error) => {
                    console.error('❌ Channel subscription error for', channelName, error);
                });
                
                // FIXED: Use direct Pusher binding since Laravel Echo .listen() stopped working after modularization
                if (channel.subscription) {
                    // Listen for new messages
                    channel.subscription.bind('MessageSent', (data) => {
                        console.log('🎉 Real-time message received via direct Pusher bind');
                        console.log('📨 Message data:', data);
                        
                        // Check if this message was sent by the current admin user
                        const currentUserId = {{ auth()->id() }};
                        
                        if (data.message && data.message.user && data.message.user.id === currentUserId) {
                            console.log('⏭️ Skipping own message');
                            return;
                        }
                        
                        // Only add to UI if this is the currently selected conversation
                        if (currentConversationId == data.message.conversation_id) {
                            console.log('✅ Adding real-time message to current conversation');
                            addMessageToContainer(data.message);
                            
                            // Scroll to bottom
                            const messagesContainer = document.getElementById('messages-container');
                            messagesContainer.scrollTop = messagesContainer.scrollHeight;
                        } else {
                            console.log('📝 Message for different conversation:', data.message.conversation_id, 'vs current:', currentConversationId);
                        }
                    });

                    // Listen for conversation termination
                    channel.subscription.bind('ConversationTerminated', (data) => {
                        console.log('🚫 Admin received conversation termination:', data);
                        console.log('🔍 Current conversation ID:', currentConversationId, 'vs event conversation ID:', data.conversation_id);
                        
                        // Handle termination for the current conversation only
                        if (currentConversationId == data.conversation_id) {
                            console.log('✅ Processing termination for current conversation');
                            
                            // Update conversation state
                            updateConversationState(data.conversation_id, {
                                status: 'completed',
                                terminated: true,
                                ended_at: data.timestamp,
                                terminatedBy: data.terminatedBy
                            });
                            
                            // Always disable interface regardless of who terminated
                            handleAdminConversationTerminated(data);
                            
                            console.log('🔄 Termination handled, interface should be disabled');
                        } else {
                            console.log('⏭️ Termination event for different conversation, skipping UI update');
                        }
                        
                        // Always reload conversations list to update status indicators
                        setTimeout(() => {
                            console.log('🔄 Reloading conversations list after termination');
                            loadConversations();
                        }, 500);
                    });
                }
                
            } catch (error) {
                console.error('Error subscribing to channel', channelName, error);
            }
        });
    }

    // Listen for real-time messages (for specific conversation when clicked)
    function listenForRealTimeMessages(conversationId) {
        // Just update the current conversation ID - channels are already subscribed
        // Note: We don't need to subscribe here anymore since we're already subscribed to all channels
    }





    // Add message to container
    function addMessageToContainer(message) {
        const messagesContainer = document.getElementById('messages-container');
        const messageElement = document.createElement('div');
        
        // Check if it's a system message
        if (message.message_type === 'system' || !message.user) {
            messageElement.className = 'mb-4 text-center';
            messageElement.innerHTML = `
                <div class="inline-block px-4 py-2 rounded-lg bg-yellow-100 text-yellow-800 border border-yellow-300">
                    <div class="font-medium text-sm">System Message</div>
                    <div class="mt-1">${message.body}</div>
                    <div class="text-xs mt-1 opacity-75">${new Date(message.created_at).toLocaleString()}</div>
                </div>
            `;
        } else {
            const isAdmin = message.user.is_admin || false;
            
            messageElement.className = `mb-4 ${isAdmin ? 'text-right' : 'text-left'}`;
            messageElement.innerHTML = `
                <div class="inline-block max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${isAdmin ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800'}">
                    <div class="font-medium text-sm">${message.user.name}</div>
                    <div class="mt-1">${message.body}</div>
                    <div class="text-xs mt-1 opacity-75">${new Date(message.created_at).toLocaleString()}</div>
                </div>
            `;
        }
        
        messagesContainer.appendChild(messageElement);
    }

    // Handle admin reply form submission
    document.getElementById('admin-reply-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const messageInput = document.getElementById('admin-message-input');
        const conversationId = document.getElementById('current-conversation-id').value;
        const messageText = messageInput.value.trim();
        
        if (!messageText || !conversationId) return;
        
        // Check if input is disabled (conversation terminated)
        if (messageInput.disabled) {
            alert('Cannot send message - conversation has been terminated');
            return;
        }
        
        try {
            const response = await fetch('/admin/chat/messages', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    conversation_id: conversationId,
                    user_id: {{ auth()->id() }},
                    content: messageText
                })
            });

            const data = await response.json();

            if (response.ok) {
                addMessageToContainer(data);
                messageInput.value = '';
                
                // Scroll to bottom
                const messagesContainer = document.getElementById('messages-container');
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            } else {
                console.error('Failed to send message:', data.message);
                if (data.message && data.message.includes('terminated')) {
                    // Conversation was terminated - disable interface
                    handleAdminConversationTerminated({
                        conversation_id: conversationId,
                        terminatedBy: 'unknown',
                        message: 'Conversation was terminated'
                    });
                }
                alert('Failed to send message: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Failed to send message:', error);
            alert('Failed to send message');
        }
    });

    // Terminate conversation function
    window.terminateConversation = async function() {
        const conversationId = document.getElementById('current-conversation-id').value;
        
        if (!conversationId) {
            alert('No active conversation to terminate');
            return;
        }
        
        if (confirm('Are you sure you want to end this chat? This action cannot be undone.')) {
            // Immediately disable the interface to prevent further messages
            const messageInput = document.getElementById('admin-message-input');
            const sendButton = document.querySelector('#admin-reply-form button[type="submit"]');
            const terminateBtn = document.getElementById('terminate-conversation-btn');
            
            if (messageInput) {
                messageInput.disabled = true;
                messageInput.placeholder = 'Terminating chat...';
            }
            if (sendButton) {
                sendButton.disabled = true;
                sendButton.textContent = 'Terminating...';
            }
            if (terminateBtn) {
                terminateBtn.disabled = true;
                terminateBtn.textContent = 'Terminating...';
            }
            
            try {
                const response = await fetch(`/chat/terminate/${conversationId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    console.log('✅ [ADMIN] Chat terminated successfully');
                    
                    // Immediately and permanently disable interface
                    const statusElement = document.getElementById('conversation-status');
                    const messageInput = document.getElementById('admin-message-input');
                    const sendButton = document.querySelector('#admin-reply-form button[type="submit"]');
                    const terminateBtn = document.getElementById('terminate-conversation-btn');
                    
                    if (statusElement) {
                        statusElement.textContent = 'Chat Terminated by You';
                        statusElement.className = 'text-sm text-red-600 font-medium';
                    }
                    
                    if (messageInput) {
                        messageInput.disabled = true;
                        messageInput.placeholder = 'Chat has been terminated by you';
                        messageInput.style.backgroundColor = '#f3f4f6';
                        messageInput.style.cursor = 'not-allowed';
                    }
                    if (sendButton) {
                        sendButton.disabled = true;
                        sendButton.style.display = 'none';
                    }
                    if (terminateBtn) {
                        terminateBtn.style.display = 'none';
                    }
                    
                    // Add permanent termination notice
                    const replyFormContainer = document.getElementById('reply-form-container');
                    if (replyFormContainer) {
                        const existingNotice = document.getElementById('termination-notice');
                        if (!existingNotice) {
                            const terminationNotice = document.createElement('div');
                            terminationNotice.id = 'termination-notice';
                            terminationNotice.className = 'bg-red-50 border border-red-200 rounded-lg p-4 mb-4 text-center';
                            terminationNotice.innerHTML = `
                                <div class="text-red-800 font-medium text-lg">
                                    🚫 Chat Terminated by You
                                </div>
                                <div class="text-red-600 text-sm mt-1">
                                    You have ended this conversation. No further messages can be sent.
                                </div>
                            `;
                            replyFormContainer.insertBefore(terminationNotice, replyFormContainer.firstChild);
                        }
                    }
                    
                    // Add termination message to the chat (don't wait for real-time event)
                    const terminationMessage = {
                        id: Date.now(),
                        body: 'Conversation ended by you (agent).',
                        user: null,
                        created_at: new Date().toISOString(),
                        message_type: 'system'
                    };
                    addMessageToContainer(terminationMessage);
                    
                    // Force scroll to bottom
                    const messagesContainer = document.getElementById('messages-container');
                    if (messagesContainer) {
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }
                    
                    // Reload conversations list to update status
                    setTimeout(() => {
                        loadConversations();
                    }, 500);
                } else {
                    // Re-enable interface if termination failed
                    if (messageInput) {
                        messageInput.disabled = false;
                        messageInput.placeholder = 'Type your reply...';
                        messageInput.style.backgroundColor = '';
                        messageInput.style.cursor = '';
                    }
                    if (sendButton) {
                        sendButton.disabled = false;
                        sendButton.style.display = 'inline-flex';
                        sendButton.style.visibility = 'visible';
                        sendButton.textContent = 'Send';
                    }
                    if (terminateBtn) {
                        terminateBtn.disabled = false;
                        terminateBtn.textContent = 'End Chat';
                    }
                    
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                
                // Re-enable interface on error
                if (messageInput) {
                    messageInput.disabled = false;
                    messageInput.placeholder = 'Type your reply...';
                    messageInput.style.backgroundColor = '';
                    messageInput.style.cursor = '';
                }
                if (sendButton) {
                    sendButton.disabled = false;
                    sendButton.style.display = 'inline-flex';
                    sendButton.style.visibility = 'visible';
                    sendButton.textContent = 'Send';
                }
                if (terminateBtn) {
                    terminateBtn.disabled = false;
                    terminateBtn.textContent = 'End Chat';
                }
                
                alert('Failed to terminate chat');
            }
        }
    };

    // Handle conversation termination by customer or admin
    function handleAdminConversationTerminated(data) {
        console.log('🚫 Handling admin conversation termination:', data);
        
        const terminatedBy = data.terminatedBy || 'unknown';
        
        // Immediately disable the message input and form
        const messageInput = document.getElementById('admin-message-input');
        const sendButton = document.querySelector('#admin-reply-form button[type="submit"]');
        const terminateBtn = document.getElementById('terminate-conversation-btn');
        const statusElement = document.getElementById('conversation-status');
        
        if (messageInput) {
            messageInput.disabled = true;
            messageInput.placeholder = `Chat terminated by ${terminatedBy === 'admin' ? 'you/another admin' : 'customer'}`;
            messageInput.style.backgroundColor = '#f3f4f6';
        }
        if (sendButton) {
            sendButton.disabled = true;
            sendButton.style.display = 'none';
        }
        if (terminateBtn) {
            terminateBtn.style.display = 'none';
        }
        if (statusElement) {
            statusElement.textContent = `Chat Terminated by ${terminatedBy === 'admin' ? 'Admin' : 'Customer'}`;
            statusElement.className = 'text-sm text-red-600 font-medium';
        }
        
        // Add prominent termination notice
        const replyFormContainer = document.getElementById('reply-form-container');
        if (replyFormContainer) {
            const existingNotice = document.getElementById('termination-notice');
            if (!existingNotice) {
                const terminationNotice = document.createElement('div');
                terminationNotice.id = 'termination-notice';
                terminationNotice.className = 'bg-red-50 border border-red-200 rounded-lg p-4 mb-4 text-center';
                
                if (terminatedBy === 'admin') {
                    terminationNotice.innerHTML = `
                        <div class="text-red-800 font-medium text-lg">
                            🚫 Chat Terminated by Admin
                        </div>
                        <div class="text-red-600 text-sm mt-1">
                            This conversation was ended by an administrator.
                        </div>
                    `;
                } else {
                    terminationNotice.innerHTML = `
                        <div class="text-red-800 font-medium text-lg">
                            🚫 Chat Terminated by Customer
                        </div>
                        <div class="text-red-600 text-sm mt-1">
                            The customer has ended this conversation.
                        </div>
                    `;
                }
                
                replyFormContainer.insertBefore(terminationNotice, replyFormContainer.firstChild);
            }
        }
        
        // Add termination message to the chat
        const terminationMessage = {
            id: Date.now(),
            body: `Conversation ended by ${terminatedBy === 'admin' ? 'admin' : 'customer'}.`,
            user: null,
            created_at: new Date().toISOString(),
            message_type: 'system'
        };
        addMessageToContainer(terminationMessage);
        
        // Reload conversations list to update status
        setTimeout(() => {
            loadConversations();
        }, 500);
    }

});
</script>
@endsection 