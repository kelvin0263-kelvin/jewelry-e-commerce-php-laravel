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
        <!-- Back to Self-Service Button -->
        <div style="padding: 10px; background: #f8f9fa; border-bottom: 1px solid #eee; text-align: center;">
            <button onclick="backToSelfService()" style="background: none; border: none; color: #666; cursor: pointer; font-size: 12px;">
                ← Back to Help Options
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
    
    window.conversationId = null;
    let isShowingSelfService = false;

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
            chatMessages.innerHTML = `
                <div id="queue-status" style="text-align: center; padding: 20px;">
                    <div style="font-size: 18px; margin-bottom: 10px;">🎫 You're in the queue</div>
                    <div style="font-size: 24px; font-weight: bold; color: #2563eb;">Position: #${queueStatus.position}</div>
                    <div style="margin: 10px 0; color: #6b7280;">Estimated wait time: ${queueStatus.estimated_wait} minutes</div>
                    <div style="margin-top: 15px;">
                        <button onclick="leaveQueue()" style="background: #ef4444; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">
                            Leave Queue
                        </button>
                    </div>
                    <div style="margin-top: 10px; font-size: 12px; color: #9ca3af;">
                        We'll connect you with the next available agent
                    </div>
                </div>
            `;
        } else {
            // Chat is already assigned or completed
            fetchMessages();
            listenForMessages();
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
        chatMessages.appendChild(messageElement);
        chatMessages.scrollTop = chatMessages.scrollHeight; // Scroll to bottom
    }

    // Function to handle form submission
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!window.conversationId || chatInput.value.trim() === '') return;

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
                    conversation_id: window.conversationId,
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
        if (window.Echo && window.conversationId) {
            const channelName = 'chat.' + window.conversationId;
            console.log('🎧 Customer listening on channel:', channelName);
            
            const channel = window.Echo.private(channelName);
            
            // FIXED: Use direct Pusher binding since Laravel Echo .listen() stopped working after modularization
            if (channel.subscription) {
                channel.subscription.bind('MessageSent', (data) => {
                    console.log('📨 Customer received message event via direct Pusher bind:', data);
                    
                    // Only add message if it's not from the current user (prevent duplication)
                    const currentUserId = {{ auth()->id() }};
                    console.log('👤 Customer user ID:', currentUserId, 'Message user ID:', data.message?.user?.id);
                    
                    if (data.message && data.message.user && data.message.user.id !== currentUserId) {
                        console.log('✅ Adding message to customer chat');
                        addMessageToBox(data.message);
                    } else {
                        console.log('⏭️ Skipping own message in customer chat');
                    }
                });
            }
            
            channel
                .error((error) => {
                    console.error('❌ Customer channel subscription error:', error);
                });
        } else {
            console.log('❌ Echo or conversationId not available for customer chat');
        }
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

function startLiveChat() {
    const selfServiceBox = document.getElementById('self-service-box');
    const chatBox = document.getElementById('chat-box');
    
    if (selfServiceBox) selfServiceBox.style.display = 'none';
    if (chatBox) chatBox.style.display = 'flex';
    
    // Start new queue-based chat
    startQueueChat();
}

function backToSelfService() {
    const chatBox = document.getElementById('chat-box');
    const selfServiceBox = document.getElementById('self-service-box');
    
    if (chatBox) chatBox.style.display = 'none';
    if (selfServiceBox) selfServiceBox.style.display = 'block';
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

// Make functions globally available
window.startLiveChat = startLiveChat;
window.backToSelfService = backToSelfService;
window.createTicket = createTicket;
@endauth

</script>
</script>