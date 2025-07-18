@auth
<div id="chat-widget-container" style="position: fixed; bottom: 20px; right: 20px; width: 350px; z-index: 1000;">
    <!-- Chat Header/Toggle Button -->
    <div id="chat-toggle" style="background-color: #2d3748; color: white; padding: 10px 15px; border-radius: 8px 8px 0 0; cursor: pointer; text-align: center; font-weight: bold;">
        Chat with Support
    </div>

    <!-- Chat Box -->
    <div id="chat-box" style="display: none; border: 1px solid #ccc; background-color: white; height: 400px; flex-direction: column; border-radius: 0 0 8px 8px;">
        <!-- Message Display Area -->
        <div id="chat-messages" style="flex-grow: 1; padding: 10px; overflow-y: auto; border-bottom: 1px solid #eee;">
            <p style="text-align: center; color: #999;">Loading chat...</p>
        </div>
        <!-- Message Input Form -->
        <form id="chat-form" style="padding: 10px; display: flex;">
            <input type="text" id="chat-input" placeholder="Type your message..." style="flex-grow: 1; border: 1px solid #ccc; padding: 8px; border-radius: 5px;">
            <button type="submit" style="margin-left: 10px; background-color: #4a5568; color: white; padding: 8px 12px; border: none; border-radius: 5px;">Send</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatToggle = document.getElementById('chat-toggle');
    const chatBox = document.getElementById('chat-box');
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    
    let conversationId = null;

    // Toggle chat box visibility
    chatToggle.addEventListener('click', () => {
        if (chatBox.style.display === 'none') {
            chatBox.style.display = 'flex';
            if (!conversationId) {
                initializeChat();
            }
        } else {
            chatBox.style.display = 'none';
        }
    });

    // Function to initialize chat (get or create conversation)
    async function initializeChat() {
        try {
            // This is a placeholder route. We will create it next.
            const response = await fetch('/chat/start', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
            });
            const data = await response.json();
            conversationId = data.id;
            
            console.log('Client chat initialized with conversation ID:', conversationId);
            console.log('Client will subscribe to channel: chat.' + conversationId);
            
            // Fetch existing messages
            fetchMessages();

            // Listen for new messages on the private channel
            listenForMessages();
        } catch (error) {
            chatMessages.innerHTML = '<p style="color: red;">Could not start chat.</p>';
        }
    }

    // Function to fetch messages
    async function fetchMessages() {
        if (!conversationId) return;
        const response = await fetch(`/admin/chat/conversations/${conversationId}/messages`);
        const messages = await response.json();
        chatMessages.innerHTML = ''; // Clear loading message
        messages.forEach(addMessageToBox);
    }

    // Function to add a message to the chat box
    function addMessageToBox(message) {
        const messageElement = document.createElement('div');
        const isMe = message.user.id === {{ auth()->id() }};
        messageElement.style.marginBottom = '10px';
        messageElement.style.textAlign = isMe ? 'right' : 'left';
        
        // Handle both 'body' and 'content' fields
        const messageContent = message.body || message.content || '';
        
        messageElement.innerHTML = `
            <div style="display: inline-block; padding: 8px 12px; border-radius: 10px; background-color: ${isMe ? '#3182ce' : '#e2e8f0'}; color: ${isMe ? 'white' : 'black'};">
                <strong>${isMe ? 'You' : message.user.name}:</strong>
                <p>${messageContent}</p>
            </div>
        `;
        chatMessages.appendChild(messageElement);
        chatMessages.scrollTop = chatMessages.scrollHeight; // Scroll to bottom
    }

    // Function to handle form submission
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!conversationId || chatInput.value.trim() === '') return;

        const messageText = chatInput.value.trim();
        chatInput.value = ''; // Clear input immediately

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
                // Add the message to the chat immediately with correct structure
                addMessageToBox({
                    id: message.id,
                    body: message.body, // Use 'body' field from response
                    content: message.body, // Also set content for compatibility
                    user: message.user // Use the user object from response
                });
            } else {
                console.error('Failed to send message:', response.statusText);
                chatInput.value = messageText; // Restore message if failed
            }
        } catch (error) {
            console.error('Failed to send message:', error);
            chatInput.value = messageText; // Restore message if failed
        }
    });

    // Function to listen for real-time messages
    function listenForMessages() {
        if (window.Echo && conversationId) {
            // Try WebSocket first
            try {
                window.Echo.private('chat.' + conversationId)
                    .listen('MessageSent', (e) => {
                        console.log('New message received via WebSocket:', e.message);
                        
                        // Only add message if it's not from the current user (prevent duplication)
                        const currentUserId = {{ auth()->id() }};
                        if (e.message.user.id !== currentUserId) {
                            addMessageToBox(e.message);
                        } else {
                            console.log('Ignoring own message to prevent duplication');
                        }
                    });
            } catch (error) {
                console.log('WebSocket failed, falling back to polling:', error);
                usePollingFallback();
            }
        } else {
            // Fallback to polling if Echo is not available
            usePollingFallback();
        }
    }

    // Fallback to polling-based real-time
    function usePollingFallback() {
        if (window.PollingEcho && conversationId) {
            console.log('Using polling-based real-time system');
            window.PollingEcho.private('chat.' + conversationId)
                .listen('MessageSent', (e) => {
                    console.log('New message received via polling:', e.message);
                    
                    // Only add message if it's not from the current user (prevent duplication)
                    const currentUserId = {{ auth()->id() }};
                    if (e.message.user.id !== currentUserId) {
                        addMessageToBox(e.message);
                    } else {
                        console.log('Ignoring own message to prevent duplication');
                    }
                });
        }
    }
});
</script>
@endauth