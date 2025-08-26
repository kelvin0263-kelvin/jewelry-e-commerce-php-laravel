@extends('layouts.admin')

@section('title', 'Chat Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Chat Management</h1>
                    <p class="text-gray-600 mt-2">Manage customer conversations and messages</p>
                </div>

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
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600" id="conversation-status"></span>
                        <button type="button" id="terminate-conversation-btn" onclick="terminateConversation()" 
                                class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-sm">
                            End Chat
                        </button>
                    </div>
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
        } catch (error) {
            console.error('Failed to load conversations:', error);
            document.getElementById('conversations-list').innerHTML = '<div class="p-4 text-center text-red-500">Failed to load conversations</div>';
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
            document.getElementById('reply-form-container').style.display = 'block';
            
            // Check if conversation is terminated and disable form accordingly
            if (conversation.status === 'completed' || conversation.status === 'abandoned') {
                const statusElement = document.getElementById('conversation-status');
                const messageInput = document.getElementById('admin-message-input');
                const sendButton = document.querySelector('#admin-reply-form button[type="submit"]');
                const terminateBtn = document.getElementById('terminate-conversation-btn');
                
                if (statusElement) {
                    statusElement.textContent = `Chat ${conversation.status === 'completed' ? 'Completed' : 'Abandoned'}`;
                    statusElement.className = 'text-sm text-red-600';
                }
                
                if (messageInput) {
                    messageInput.disabled = true;
                    messageInput.placeholder = `Chat has been ${conversation.status === 'completed' ? 'completed' : 'abandoned'}`;
                }
                if (sendButton) {
                    sendButton.disabled = true;
                    sendButton.style.opacity = '0.5';
                }
                if (terminateBtn) terminateBtn.style.display = 'none';
            } else {
                // Enable form for active conversations
                const statusElement = document.getElementById('conversation-status');
                const messageInput = document.getElementById('admin-message-input');
                const sendButton = document.querySelector('#admin-reply-form button[type="submit"]');
                const terminateBtn = document.getElementById('terminate-conversation-btn');
                
                if (statusElement) {
                    statusElement.textContent = 'Active Chat';
                    statusElement.className = 'text-sm text-green-600';
                }
                
                if (messageInput) {
                    messageInput.disabled = false;
                    messageInput.placeholder = 'Type your reply...';
                }
                if (sendButton) {
                    sendButton.disabled = false;
                    sendButton.style.opacity = '1';
                }
                if (terminateBtn) terminateBtn.style.display = 'block';
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
                        
                        // Only disable if terminated by customer and this is the current conversation
                        if (data.terminated_by === 'customer' && currentConversationId == data.conversation_id) {
                            handleAdminConversationTerminated(data);
                        }
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

    // Terminate conversation function
    window.terminateConversation = async function() {
        const conversationId = document.getElementById('current-conversation-id').value;
        
        if (!conversationId) {
            alert('No active conversation to terminate');
            return;
        }
        
        if (confirm('Are you sure you want to end this chat? This action cannot be undone.')) {
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
                    // Update conversation status
                    const statusElement = document.getElementById('conversation-status');
                    if (statusElement) {
                        statusElement.textContent = 'Chat Terminated';
                        statusElement.className = 'text-sm text-red-600';
                    }
                    
                    // Disable the form
                    const messageInput = document.getElementById('admin-message-input');
                    const sendButton = document.querySelector('#admin-reply-form button[type="submit"]');
                    const terminateBtn = document.getElementById('terminate-conversation-btn');
                    
                    if (messageInput) {
                        messageInput.disabled = true;
                        messageInput.placeholder = 'Chat has been terminated';
                    }
                    if (sendButton) sendButton.disabled = true;
                    if (terminateBtn) terminateBtn.style.display = 'none';
                    
                    // Add termination message to the chat
                    const terminationMessage = {
                        id: Date.now(),
                        body: 'Conversation ended by agent.',
                        user: null,
                        created_at: new Date().toISOString(),
                        message_type: 'system'
                    };
                    addMessageToContainer(terminationMessage);
                    
                    alert('Chat terminated successfully');
                    
                    // Reload conversations list to update status
                    loadConversations();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to terminate chat');
            }
        }
    };

    // Handle conversation termination by customer
    function handleAdminConversationTerminated(data) {
        console.log('🚫 Handling admin conversation termination:', data);
        
        // Update conversation status
        const statusElement = document.getElementById('conversation-status');
        if (statusElement) {
            statusElement.textContent = 'Chat Terminated by Customer';
            statusElement.className = 'text-sm text-red-600';
        }
        
        // Disable the form
        const messageInput = document.getElementById('admin-message-input');
        const sendButton = document.querySelector('#admin-reply-form button[type="submit"]');
        const terminateBtn = document.getElementById('terminate-conversation-btn');
        
        if (messageInput) {
            messageInput.disabled = true;
            messageInput.placeholder = 'Chat has been terminated by customer';
        }
        if (sendButton) {
            sendButton.disabled = true;
            sendButton.style.opacity = '0.5';
        }
        if (terminateBtn) terminateBtn.style.display = 'none';
        
        // Add termination notification to the chat
        const messagesContainer = document.getElementById('messages-container');
        if (messagesContainer) {
            const terminationElement = document.createElement('div');
            terminationElement.className = 'mb-4 text-center';
            terminationElement.innerHTML = `
                <div class="inline-block px-4 py-3 rounded-lg bg-red-100 text-red-800 border border-red-300">
                    <div class="font-medium text-sm">🚫 Chat Terminated</div>
                    <div class="mt-1">This conversation has been ended by the customer.</div>
                    <div class="text-xs mt-2 opacity-75">${new Date().toLocaleString()}</div>
                </div>
            `;
            messagesContainer.appendChild(terminationElement);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        // Show notification
        alert('The customer has terminated this conversation.');
        
        // Reload conversations list to update status
        loadConversations();
    }

});
</script>
@endsection 