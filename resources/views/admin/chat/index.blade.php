@extends('layouts.admin')

@section('title', 'Chat Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-800">Chat Management</h1>
            <p class="text-gray-600 mt-2">Manage customer conversations and messages</p>
            <div class="mt-4">
                <button onclick="debugAdminRealTime()" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">Debug Real-Time</button>
                <button onclick="testAdminEcho()" class="bg-green-500 text-white px-4 py-2 rounded mr-2">Test Echo</button>
                <button onclick="testChannelSubscription()" class="bg-purple-500 text-white px-4 py-2 rounded">Test Channel Sub</button>
            </div>
        </div>

        <div class="flex h-96">
            <!-- Conversations List -->
            <div class="w-1/3 border-r border-gray-200 overflow-y-auto">
                <div class="p-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-700">Conversations</h2>
                </div>
                <div id="conversations-list">
                    <div class="p-4 text-center text-gray-500">
                        Loading conversations...
                    </div>
                </div>
            </div>

            <!-- Messages Display -->
            <div class="w-2/3 flex flex-col">
                <div class="p-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-700" id="chat-header">Select a conversation</h2>
                </div>
                <div class="flex-1 overflow-y-auto p-4" id="messages-container">
                    <div class="text-center text-gray-500">
                        Select a conversation to view messages
                    </div>
                </div>
                <!-- Admin Reply Form -->
                <div class="p-4 border-t border-gray-100" id="reply-form-container" style="display: none;">
                    <form id="admin-reply-form" class="flex gap-2">
                        <input type="hidden" id="current-conversation-id" value="">
                        <input type="text" id="admin-message-input" placeholder="Type your reply..." 
                               class="flex-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
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

    // Debug: Check if Echo is available when page loads
    console.log('=== ADMIN CHAT PAGE LOADED ===');
    console.log('Echo available on page load:', !!window.Echo);
    
    // Wait a moment for scripts to load, then check again
    setTimeout(() => {
        console.log('Echo available after timeout:', !!window.Echo);
        if (window.Echo) {
            console.log('✅ Echo loaded successfully on admin page');
        } else {
            console.error('❌ Echo still not available on admin page');
        }
    }, 1000);

    // Load conversations on page load
    loadConversations();

    // Load all conversations
    async function loadConversations() {
        try {
            const response = await fetch('/admin/chat/conversations');
            const conversations = await response.json();
            
            const conversationsList = document.getElementById('conversations-list');
            
            if (conversations.length === 0) {
                conversationsList.innerHTML = '<div class="p-4 text-center text-gray-500">No conversations yet</div>';
                return;
            }

            conversationsList.innerHTML = '';
            conversations.forEach(conversation => {
                const conversationElement = document.createElement('div');
                conversationElement.className = 'p-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50';
                conversationElement.innerHTML = `
                    <div class="font-medium text-gray-800">${conversation.user.name}</div>
                    <div class="text-sm text-gray-600">${conversation.user.email}</div>
                    <div class="text-xs text-gray-500 mt-1">Conversation #${conversation.id}</div>
                `;
                conversationElement.addEventListener('click', () => loadMessages(conversation.id, conversation.user.name));
                conversationsList.appendChild(conversationElement);
            });
            
            // Auto-subscribe admin to all conversation channels for real-time notifications
            subscribeToAllConversations(conversations);
            
            // Debug real-time setup
            debugAdminRealTime();
        } catch (error) {
            console.error('Failed to load conversations:', error);
            document.getElementById('conversations-list').innerHTML = '<div class="p-4 text-center text-red-500">Failed to load conversations</div>';
        }
    }

    // Load messages for a specific conversation
    async function loadMessages(conversationId, userName) {
        currentConversationId = conversationId;
        
        try {
            const response = await fetch(`/admin/chat/conversations/${conversationId}/messages`);
            const messages = await response.json();
            
            // Update header
            document.getElementById('chat-header').textContent = `Chat with ${userName}`;
            
            // Show reply form
            document.getElementById('current-conversation-id').value = conversationId;
            document.getElementById('reply-form-container').style.display = 'block';
            
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
            
            // Scroll to bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            
            // Listen for real-time messages
            listenForRealTimeMessages(conversationId);
        } catch (error) {
            console.error('Failed to load messages:', error);
            document.getElementById('messages-container').innerHTML = '<div class="text-center text-red-500">Failed to load messages</div>';
        }
    }

    // Subscribe admin to all conversation channels for real-time notifications
    function subscribeToAllConversations(conversations) {
        if (!window.Echo) {
            console.log('Echo not available, cannot subscribe to conversations');
            return;
        }
        
        console.log('Admin subscribing to all conversation channels...');
        
        conversations.forEach(conversation => {
            const channelName = 'chat.' + conversation.id;
            console.log('Admin subscribing to channel:', channelName);
            
            try {
                window.Echo.private(channelName)
                    .listen('MessageSent', (e) => {
                        console.log('New message received in admin via WebSocket:', e.message);
                        console.log('Message from user:', e.message.user.id, 'Content:', e.message.body);
                        console.log('Message conversation ID:', e.message.conversation_id);
                        
                        // Only add to UI if this is the currently selected conversation
                        if (currentConversationId == e.message.conversation_id) {
                            addMessageToContainer(e.message);
                            
                            // Scroll to bottom
                            const messagesContainer = document.getElementById('messages-container');
                            messagesContainer.scrollTop = messagesContainer.scrollHeight;
                        } else {
                            console.log('Message for different conversation, not adding to UI');
                            // Could add notification badge here in the future
                        }
                    });
            } catch (error) {
                console.log('Failed to subscribe to channel:', channelName, error);
            }
        });
    }

    // Listen for real-time messages (for specific conversation when clicked)
    function listenForRealTimeMessages(conversationId) {
        // Just update the current conversation ID - channels are already subscribed
        console.log('Admin now viewing conversation:', conversationId);
        // Note: We don't need to subscribe here anymore since we're already subscribed to all channels
    }

    // Debug: Add logging for admin real-time status  
    window.debugAdminRealTime = function() {
        console.log('=== ADMIN REAL-TIME DEBUG ===');
        console.log('Echo available:', !!window.Echo);
        console.log('Current conversation ID:', currentConversationId);
        console.log('All conversations loaded, admin subscribed to all channels');
        
        if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
            console.log('Echo connection state:', window.Echo.connector.pusher.connection.state);
            console.log('Echo socket ID:', window.Echo.connector.pusher.connection.socket_id);
        }
    }
    
    // Test Echo connection
    window.testAdminEcho = function() {
        console.log('=== TESTING ADMIN ECHO ===');
        
        if (!window.Echo) {
            console.error('❌ Echo not available');
            alert('Echo not available');
            return;
        }
        
        console.log('✅ Echo available');
        
        if (window.Echo.connector && window.Echo.connector.pusher) {
            const state = window.Echo.connector.pusher.connection.state;
            console.log('Echo connection state:', state);
            
            if (state === 'connected') {
                console.log('✅ Echo connected');
                alert('Echo connected successfully!');
            } else {
                console.log('❌ Echo not connected, state:', state);
                alert('Echo not connected, state: ' + state);
            }
                 } else {
             console.error('❌ Echo connector not available');
             alert('Echo connector not available');
         }
     }
     
         // Test channel subscription
    window.testChannelSubscription = function() {
         console.log('=== TESTING CHANNEL SUBSCRIPTION ===');
         
         if (!window.Echo) {
             console.error('❌ Echo not available');
             alert('Echo not available');
             return;
         }
         
         // Test subscribing to channel chat.3 (from your console log)
         const testChannel = 'chat.3';
         console.log('Testing subscription to channel:', testChannel);
         
         try {
             window.Echo.private(testChannel)
                 .listen('MessageSent', (e) => {
                     console.log('🎉 TEST: Received message on channel ' + testChannel + ':', e.message);
                     alert('✅ Test successful! Received message: ' + e.message.body);
                 });
             
             console.log('✅ Successfully subscribed to test channel:', testChannel);
             alert('Subscribed to channel: ' + testChannel + '\nSend a message from client to test!');
         } catch (error) {
             console.error('❌ Failed to subscribe to test channel:', error);
             alert('Failed to subscribe to channel: ' + error.message);
         }
     }

    // Add message to container
    function addMessageToContainer(message) {
        const messagesContainer = document.getElementById('messages-container');
        const messageElement = document.createElement('div');
        const isAdmin = message.user.is_admin || false;
        
        messageElement.className = `mb-4 ${isAdmin ? 'text-right' : 'text-left'}`;
        messageElement.innerHTML = `
            <div class="inline-block max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${isAdmin ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800'}">
                <div class="font-medium text-sm">${message.user.name}</div>
                <div class="mt-1">${message.body}</div>
                <div class="text-xs mt-1 opacity-75">${new Date(message.created_at).toLocaleString()}</div>
            </div>
        `;
        messagesContainer.appendChild(messageElement);
    }

    // Handle admin reply form submission
    document.getElementById('admin-reply-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const messageInput = document.getElementById('admin-message-input');
        const conversationId = document.getElementById('current-conversation-id').value;
        const messageText = messageInput.value.trim();
        
        if (!messageText || !conversationId) return;
        
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

            if (response.ok) {
                const message = await response.json();
                console.log('Admin message sent successfully:', message);
                addMessageToContainer(message);
                messageInput.value = '';
                
                // Scroll to bottom
                const messagesContainer = document.getElementById('messages-container');
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            } else {
                alert('Failed to send message');
            }
        } catch (error) {
            console.error('Failed to send message:', error);
            alert('Failed to send message');
        }
    });
});
</script>
@endsection 