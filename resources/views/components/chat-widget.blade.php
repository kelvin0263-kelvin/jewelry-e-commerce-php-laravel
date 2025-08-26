<!-- Self-Service Chat Widget - Available for all users -->
<div id="chat-widget-container" style="position: fixed; bottom: 20px; right: 20px; width: 350px; z-index: 1000;">
    <!-- Chat Header/Toggle Button -->
    <div id="chat-toggle" style="background-color: #2d3748; color: white; padding: 10px 15px; border-radius: 8px 8px 0 0; cursor: pointer; text-align: center; font-weight: bold;">
        💬 Need Help?
    </div>

    <!-- Self-Service Options -->
    <div id="self-service-box" style="display: none; border: 1px solid #ccc; background-color: white; max-height: 500px; overflow-y: auto; border-radius: 0 0 8px 8px;">
        <!-- Header -->
        <div style="padding: 15px; border-bottom: 1px solid #eee; background-color: #f8f9fa;">
            <h3 style="margin: 0; font-size: 16px; font-weight: bold; color: #333;">How can we help you?</h3>
            <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">Choose an option below or start a live chat</p>
        </div>
        
        <!-- Quick Help Options -->
        <div style="padding: 15px;">
            <div style="margin-bottom: 15px;">
                <button onclick="quickHelp('track_order')" style="width: 100%; text-align: left; padding: 12px; border: 1px solid #e0e0e0; border-radius: 6px; background: white; cursor: pointer; margin-bottom: 8px; transition: all 0.2s;">
                    <div style="display: flex; align-items: center;">
                        <span style="font-size: 18px; margin-right: 10px;">📦</span>
                        <div>
                            <div style="font-weight: bold; font-size: 14px;">Track My Order</div>
                            <div style="font-size: 12px; color: #666;">Check order status & shipping</div>
                        </div>
                    </div>
                </button>
                
                <button onclick="quickHelp('return_item')" style="width: 100%; text-align: left; padding: 12px; border: 1px solid #e0e0e0; border-radius: 6px; background: white; cursor: pointer; margin-bottom: 8px; transition: all 0.2s;">
                    <div style="display: flex; align-items: center;">
                        <span style="font-size: 18px; margin-right: 10px;">↩️</span>
                        <div>
                            <div style="font-weight: bold; font-size: 14px;">Return or Exchange</div>
                            <div style="font-size: 12px; color: #666;">Start a return or exchange</div>
                        </div>
                    </div>
                </button>
                
                <button onclick="quickHelp('product_info')" style="width: 100%; text-align: left; padding: 12px; border: 1px solid #e0e0e0; border-radius: 6px; background: white; cursor: pointer; margin-bottom: 8px; transition: all 0.2s;">
                    <div style="display: flex; align-items: center;">
                        <span style="font-size: 18px; margin-right: 10px;">💎</span>
                        <div>
                            <div style="font-weight: bold; font-size: 14px;">Product Information</div>
                            <div style="font-size: 12px; color: #666;">Care, authenticity, materials</div>
                        </div>
                    </div>
                </button>
                
                <button onclick="quickHelp('account_help')" style="width: 100%; text-align: left; padding: 12px; border: 1px solid #e0e0e0; border-radius: 6px; background: white; cursor: pointer; margin-bottom: 8px; transition: all 0.2s;">
                    <div style="display: flex; align-items: center;">
                        <span style="font-size: 18px; margin-right: 10px;">👤</span>
                        <div>
                            <div style="font-weight: bold; font-size: 14px;">Account & Profile</div>
                            <div style="font-size: 12px; color: #666;">Password, settings, profile</div>
                        </div>
                    </div>
                </button>
                
                <button onclick="createTicket()" style="width: 100%; text-align: left; padding: 12px; border: 1px solid #10b981; border-radius: 6px; background: #10b981; color: white; cursor: pointer; margin-bottom: 15px; transition: all 0.2s;">
                    <div style="display: flex; align-items: center;">
                        <span style="font-size: 18px; margin-right: 10px;">🎫</span>
                        <div>
                            <div style="font-weight: bold; font-size: 14px;">Create Support Ticket</div>
                            <div style="font-size: 12px; color: #f0fff4;">For detailed issues or non-urgent help</div>
                        </div>
                    </div>
                </button>
            </div>
            
            <!-- Browse All Help -->
            <button onclick="openSelfService()" style="width: 100%; padding: 10px; background: #f0f0f0; border: 1px solid #ddd; border-radius: 6px; cursor: pointer; margin-bottom: 10px;">
                <span style="font-size: 14px; color: #333;">📚 Browse All Help Topics</span>
            </button>
            
            <!-- Live Chat Option -->
            <div style="border-top: 1px solid #eee; padding-top: 15px; margin-top: 15px;">
                <p style="font-size: 12px; color: #666; margin: 0 0 10px 0; text-align: center;">Can't find what you need?</p>
                @auth
                    <button onclick="startLiveChat()" style="width: 100%; padding: 12px; background: #2d3748; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
                        💬 Chat with an Agent
                    </button>
                @else
                    <div style="text-align: center;">
                        <a href="{{ route('login') }}" style="display: inline-block; padding: 8px 16px; background: #2d3748; color: white; text-decoration: none; border-radius: 6px; font-size: 12px;">
                            Login for Live Chat
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    @auth
    <!-- Live Chat Box (hidden by default) -->
    <div id="chat-box" style="display: none; border: 1px solid #ccc; background-color: white; height: 400px; flex-direction: column; border-radius: 0 0 8px 8px;">
        <!-- Chat Controls -->
        <div style="padding: 10px; background: #f8f9fa; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
            <button onclick="backToSelfService()" style="background: none; border: none; color: #666; cursor: pointer; font-size: 12px;">
                ← Back to Help Options
            </button>
            <button id="terminate-chat-btn" onclick="terminateChat()" style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 11px; display: none;">
                End Chat
            </button>
        </div>
        
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
    @endauth
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatToggle = document.getElementById('chat-toggle');
    const selfServiceBox = document.getElementById('self-service-box');
    const chatBox = document.getElementById('chat-box');
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    
    window.conversationId = localStorage.getItem('activeConversationId') || null;
    let isShowingSelfService = false;
    
    // Check for URL parameter to open specific chat
    const urlParams = new URLSearchParams(window.location.search);
    const openChatId = urlParams.get('open_chat');
    
    if (openChatId) {
        // Auto-open chat widget with specific conversation
        setTimeout(() => {
            startLiveChat(parseInt(openChatId));
        }, 1000);
    }

    // Toggle between self-service and hidden
    chatToggle.addEventListener('click', () => {
        if (!isShowingSelfService) {
            // Show self-service options first
            selfServiceBox.style.display = 'block';
            if (chatBox) chatBox.style.display = 'none';
            isShowingSelfService = true;
        } else {
            // Hide everything
            selfServiceBox.style.display = 'none';
            if (chatBox) chatBox.style.display = 'none';
            isShowingSelfService = false;
        }
    });

    // Function to initialize chat (get or create conversation)
    window.initializeChat = async function initializeChat() {
        try {
            // This is a placeholder route. We will create it next.
            const response = await fetch('/chat/start', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
            });
            const data = await response.json();
            window.conversationId = data.id;
            

            
            // Fetch existing messages
            fetchMessages();

            // Listen for new messages on the private channel
            listenForMessages();
        } catch (error) {
            chatMessages.innerHTML = '<p style="color: red;">Could not start chat.</p>';
        }
    }

    // New queue-based chat initialization
    async function startQueueChat() {
        try {
            const escalationContext = null; // Will be passed from server if available
            
            const response = await fetch('/chat/start', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    escalation_context: escalationContext,
                    initial_message: 'Hello, I need assistance.'
                })
            });

            if (!response.ok) {
                throw new Error('Failed to start chat');
            }

            const data = await response.json();
            window.conversationId = data.conversation_id;
            localStorage.setItem('activeConversationId', data.conversation_id);
            
            // Check if this is an existing active conversation
            if (data.status === 'active' && data.existing_conversation) {
                // Show active conversation interface
                chatMessages.innerHTML = `
                    <div style="text-align: center; padding: 20px; color: #059669;">
                        <div style="font-size: 18px; margin-bottom: 10px;">✅ Resuming Active Chat</div>
                        <div>You have an active conversation with an agent.</div>
                    </div>
                `;
                
                // Show terminate button for active conversation
                showTerminateButton();
                
                // Load existing messages and listen for new ones
                setTimeout(() => {
                    fetchMessages();
                    listenForMessages();
                }, 1000);
                
                return;
            }
            
            // Show queue status
            showQueueStatus(data.queue_status);
            
            // Start polling for queue updates
            startQueuePolling();
            
        } catch (error) {
            console.error('Error starting queue chat:', error);
            chatMessages.innerHTML = '<p style="color: red;">Could not join chat queue. Please try again.</p>';
        }
    }

    function showQueueStatus(queueStatus) {
        if (queueStatus.in_queue) {
            // Show queue status but allow messaging
            const queueStatusDiv = `
                <div id="queue-status" style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 15px; margin-bottom: 15px;">
                    <div style="text-align: center;">
                        <div style="font-size: 16px; margin-bottom: 8px; color: #1d4ed8;">🎫 You're in the queue</div>
                        <div style="font-size: 20px; font-weight: bold; color: #2563eb;">Position: #${queueStatus.position}</div>
                        <div style="margin: 8px 0; color: #6b7280; font-size: 14px;">Estimated wait: ${queueStatus.estimated_wait} minutes</div>
                        <div style="margin-top: 10px;">
                            <button onclick="leaveQueue()" style="background: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                Leave Queue
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Initialize messages area with queue status
            chatMessages.innerHTML = queueStatusDiv + '<div id="queue-messages"></div>';
            
            // Enable messaging while in queue
            enableQueueMessaging();
            
            // Load any existing messages
            fetchMessages();
            listenForMessages();
        } else {
            // Chat is already assigned or completed
            fetchMessages();
            listenForMessages();
        }
    }
    
    function enableQueueMessaging() {
        // Enable the chat input for queue messages
        const chatInput = document.getElementById('chat-input');
        const chatForm = document.getElementById('chat-form');
        
        if (chatInput && chatForm) {
            chatInput.disabled = false;
            chatInput.placeholder = 'You can send messages while waiting...';
            chatForm.style.opacity = '1';
            chatForm.style.pointerEvents = 'auto';
        }
    }

    function startQueuePolling() {
        // Clear any existing interval
        if (window.queueCheckInterval) {
            clearInterval(window.queueCheckInterval);
        }

        window.queueCheckInterval = setInterval(async () => {
            if (!window.conversationId) return;

            try {
                const response = await fetch(`/chat/queue-status/${window.conversationId}`);
                const queueStatus = await response.json();

                if (!queueStatus.in_queue) {
                    // Chat has been assigned to an agent
                    clearInterval(window.queueCheckInterval);
                    chatMessages.innerHTML = `
                        <div style="text-align: center; padding: 20px; color: #059669;">
                            <div style="font-size: 18px; margin-bottom: 10px;">✅ Connected to Agent</div>
                            <div>You can now start chatting!</div>
                        </div>
                    `;
                    
                    // Show terminate button when connected to agent
                    showTerminateButton();
                    
                    // Load chat interface
                    setTimeout(() => {
                        fetchMessages();
                        listenForMessages();
                    }, 2000);
                } else {
                    // Update queue position
                    const positionElement = document.querySelector('#queue-status');
                    if (positionElement) {
                        showQueueStatus(queueStatus);
                    }
                }
            } catch (error) {
                console.error('Error checking queue status:', error);
            }
        }, 5000); // Check every 5 seconds
    }

    async function leaveQueue() {
        if (!window.conversationId) return;

        if (confirm('Are you sure you want to leave the queue?')) {
            try {
                const response = await fetch(`/chat/leave-queue/${window.conversationId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    clearInterval(window.queueCheckInterval);
                    chatMessages.innerHTML = `
                        <div style="text-align: center; padding: 20px; color: #6b7280;">
                            <div style="font-size: 18px; margin-bottom: 10px;">👋 Left Queue</div>
                            <div>You have left the chat queue.</div>
                            <button onclick="startQueueChat()" style="background: #2563eb; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; margin-top: 10px;">
                                Join Queue Again
                            </button>
                        </div>
                    `;
                    window.conversationId = null;
                    localStorage.removeItem('activeConversationId');
                } else {
                    alert('Failed to leave queue. Please try again.');
                }
            } catch (error) {
                console.error('Error leaving queue:', error);
                alert('Failed to leave queue. Please try again.');
            }
        }
    }

    // Make functions globally available
    window.initializeChat = initializeChat;
    window.startQueueChat = startQueueChat;
            window.leaveQueue = leaveQueue;
        window.createTicket = createTicket;

    // Function to fetch messages
    async function fetchMessages() {
        if (!window.conversationId) return;
        const response = await fetch(`/admin/chat/conversations/${window.conversationId}/messages`);
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
        // Check if we're in queue mode and append to the correct container
        const queueMessagesContainer = document.getElementById('queue-messages');
        const targetContainer = queueMessagesContainer || chatMessages;
        
        targetContainer.appendChild(messageElement);
        
        // Scroll the main chat container to bottom
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Function to handle form submission
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!window.conversationId || chatInput.value.trim() === '') return;

        const messageText = chatInput.value.trim();
        chatInput.value = ''; // Clear input immediately

        try {
            const response = await fetch('/chat/messages', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    conversation_id: window.conversationId,
                    body: messageText
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

    // Function to listen for real-time messages using direct Echo
    function listenForMessages() {
        if (!window.conversationId) {
            console.log('❌ No conversation ID available');
            return;
        }

        console.log('🎧 Setting up direct Echo message listening for conversation:', window.conversationId);
        
        // Subscribe to the conversation channel directly
        window.Echo.private(`conversation.${window.conversationId}`)
            .listen('MessageSent', (e) => {
                const currentUserId = {{ auth()->id() }};
                
                if (e.message && e.message.user && e.message.user.id !== currentUserId) {
                    console.log('✅ Adding message to customer chat');
                    addMessageToBox(e.message);
                } else if (e.message && e.message.message_type === 'system') {
                    console.log('✅ Adding system message to customer chat');
                    addSystemMessageToBox(e.message);
                }
            })
            .listen('ConversationTerminated', (e) => {
                if (e.terminatedBy === 'admin') {
                    console.log('🚫 Customer received termination from admin');
                    handleConversationTerminated(e, 'admin');
                }
            });
    }
    

});

// Self-service functions (available to all users)
function quickHelp(helpType) {
    const solutions = {
        'track_order': 'To track your order:\n1. Check your email for tracking info\n2. Log into your account\n3. Visit Orders section\n\nStill need help?',
        'return_item': 'To return an item:\n1. Items must be unworn\n2. Original packaging required\n3. Within 30 days\n4. Contact us for return label\n\nNeed to start a return?',
        'product_info': 'Product Information:\n• All jewelry is authentic\n• Certificates included\n• Care instructions provided\n• Materials: Gold, Silver, Gemstones\n\nSpecific questions?',
        'account_help': 'Account Help:\n• Reset password: Use "Forgot Password"\n• Update profile: Account settings\n• Order history: Orders section\n\nNeed more help?'
    };
    
    const solution = solutions[helpType] || 'How can we help you with this?';
    
    if (confirm(solution + '\n\nClick OK to chat with an agent, or Cancel to browse more help.')) {
        @auth
            startLiveChat();
        @else
            window.location.href = '{{ route("login") }}';
        @endauth
    } else {
        openSelfService();
    }
}

function openSelfService() {
    window.location.href = '/self-service';
}

@auth
// Make conversationId and functions globally available
window.conversationId = null;
window.queueCheckInterval = null;

function startLiveChat(existingConversationId = null) {
    const selfServiceBox = document.getElementById('self-service-box');
    const chatBox = document.getElementById('chat-box');
    
    if (selfServiceBox) selfServiceBox.style.display = 'none';
    if (chatBox) chatBox.style.display = 'flex';
    
    // Check if we have an existing conversation ID from a previous session
    const conversationToResume = existingConversationId || window.conversationId;
    
    if (conversationToResume) {
        // Resume existing conversation
        resumeExistingChat(conversationToResume);
    } else {
        // Start new queue-based chat
        startQueueChat();
    }
}

async function resumeExistingChat(conversationId) {
    try {
        window.conversationId = conversationId;
        localStorage.setItem('activeConversationId', conversationId);
        
        // Show resuming message
        chatMessages.innerHTML = `
            <div style="text-align: center; padding: 20px; color: #059669;">
                <div style="font-size: 18px; margin-bottom: 10px;">🔄 Resuming Chat</div>
                <div>Reconnecting to your active conversation...</div>
            </div>
        `;
        
        // Show terminate button for active conversation
        showTerminateButton();
        
        // Load existing messages and listen for new ones
        setTimeout(() => {
            fetchMessages();
            listenForMessages();
        }, 1000);
        
    } catch (error) {
        console.error('Error resuming chat:', error);
        chatMessages.innerHTML = '<p style="color: red;">Could not resume chat. Please try again.</p>';
    }
}

function backToSelfService() {
    const chatBox = document.getElementById('chat-box');
    const selfServiceBox = document.getElementById('self-service-box');
    
    if (chatBox) chatBox.style.display = 'none';
    if (selfServiceBox) selfServiceBox.style.display = 'block';
    
    // Don't clear conversation ID - preserve the conversation state
    // This allows customers to return to their chat without losing their place
}

function createTicket() {
    // Check if user is authenticated
    @auth
        // Open ticket creation page in new tab
        window.open('{{ route('tickets.create') }}', '_blank');
    @else
        // Redirect to login with redirect back to ticket creation
        window.location.href = '{{ route('login') }}?redirect=' + encodeURIComponent('{{ route('tickets.create') }}');
    @endauth
}

function terminateChat() {
    if (!window.conversationId) {
        alert('No active conversation to terminate');
        return;
    }
    
    if (confirm('Are you sure you want to end this chat? This action cannot be undone.')) {
        fetch(`/chat/terminate/${window.conversationId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hide terminate button
                const terminateBtn = document.getElementById('terminate-chat-btn');
                if (terminateBtn) terminateBtn.style.display = 'none';
                
                // Disable message input
                const chatInput = document.getElementById('chat-input');
                const chatForm = document.getElementById('chat-form');
                if (chatInput) {
                    chatInput.disabled = true;
                    chatInput.placeholder = 'Chat has been terminated';
                }
                if (chatForm) {
                    chatForm.style.opacity = '0.5';
                }
                
                // Show termination message
                const chatMessages = document.getElementById('chat-messages');
                if (chatMessages) {
                    const terminationMessage = document.createElement('div');
                    terminationMessage.style.cssText = 'text-align: center; color: #666; font-style: italic; margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px;';
                    terminationMessage.textContent = 'Chat has been terminated. Thank you for contacting us!';
                    chatMessages.appendChild(terminationMessage);
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
                
                // Clear the stored conversation ID
                localStorage.removeItem('activeConversationId');
                window.conversationId = null;
                
                alert('Chat terminated successfully');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to terminate chat');
        });
    }
}

function showTerminateButton() {
    const terminateBtn = document.getElementById('terminate-chat-btn');
    if (terminateBtn) {
        terminateBtn.style.display = 'block';
    }
}

function hideTerminateButton() {
    const terminateBtn = document.getElementById('terminate-chat-btn');
    if (terminateBtn) {
        terminateBtn.style.display = 'none';
    }
}

function addSystemMessageToBox(message) {
    const chatMessages = document.getElementById('chat-messages');
    if (!chatMessages) return;
    
    const messageElement = document.createElement('div');
    messageElement.style.cssText = 'text-align: center; color: #666; font-style: italic; margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px; border: 1px solid #e9ecef;';
    messageElement.innerHTML = `
        <div style="font-size: 12px; color: #999; margin-bottom: 5px;">System Message</div>
        <div>${message.body}</div>
        <div style="font-size: 11px; color: #999; margin-top: 5px;">${new Date(message.created_at).toLocaleString()}</div>
    `;
    
    chatMessages.appendChild(messageElement);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function handleConversationTerminated(data, terminatedBy) {
    console.log('🚫 Handling conversation termination:', data);
    
    // Hide terminate button
    hideTerminateButton();
    
    // Clear stored conversation ID
    localStorage.removeItem('activeConversationId');
    window.conversationId = null;
    
    // Disable message input
    const chatInput = document.getElementById('chat-input');
    const chatForm = document.getElementById('chat-form');
    if (chatInput) {
        chatInput.disabled = true;
        chatInput.placeholder = 'Chat has been terminated by ' + (terminatedBy === 'admin' ? 'agent' : 'customer');
    }
    if (chatForm) {
        chatForm.style.opacity = '0.5';
        chatForm.style.pointerEvents = 'none';
    }
    
    // Show termination notification
    const chatMessages = document.getElementById('chat-messages');
    if (chatMessages) {
        const terminationMessage = document.createElement('div');
        terminationMessage.style.cssText = 'text-align: center; color: #dc3545; font-weight: bold; margin: 15px 0; padding: 15px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px;';
        terminationMessage.innerHTML = `
            <div style="font-size: 16px; margin-bottom: 5px;">🚫 Chat Terminated</div>
            <div style="font-size: 14px;">This conversation has been ended by the ${terminatedBy === 'admin' ? 'agent' : 'customer'}.</div>
            <div style="font-size: 12px; margin-top: 8px; color: #721c24;">Thank you for contacting us!</div>
        `;
        chatMessages.appendChild(terminationMessage);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
}

// Make functions globally available
window.startLiveChat = startLiveChat;
window.backToSelfService = backToSelfService;
window.createTicket = createTicket;
window.terminateChat = terminateChat;
window.showTerminateButton = showTerminateButton;
window.hideTerminateButton = hideTerminateButton;
@endauth

</script>