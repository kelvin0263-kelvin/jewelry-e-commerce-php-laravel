<!-- Self-Service Chat Widget - Available for all users -->
<div id="chat-widget-container" style="position: fixed; bottom: 20px; right: 20px; width: 350px; z-index: 1000;">
    <!-- Chat Header/Toggle Button -->
    <div id="chat-toggle"
        style="background-color: #2d3748; color: white; padding: 10px 15px; border-radius: 8px 8px 0 0; cursor: pointer; text-align: center; font-weight: bold;">
        💬 Need Help?
    </div>

    <!-- Self-Service Options -->
    <div id="self-service-box"
        style="display: none; border: 1px solid #ccc; background-color: white; max-height: 500px; overflow-y: auto; border-radius: 0 0 8px 8px;">
        <!-- Header -->
        <div style="padding: 15px; border-bottom: 1px solid #eee; background-color: #f8f9fa;">
            <h3 style="margin: 0; font-size: 16px; font-weight: bold; color: #333;">How can we help you?</h3>
            <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">Choose an option below or start a live chat</p>
        </div>

        <!-- Quick Help Options -->
        <div style="padding: 15px;">
            <div style="margin-bottom: 15px;">
                <button onclick="quickHelp('track_order')"
                    style="width: 100%; text-align: left; padding: 12px; border: 1px solid #e0e0e0; border-radius: 6px; background: white; cursor: pointer; margin-bottom: 8px; transition: all 0.2s;">
                    <div style="display: flex; align-items: center;">
                        <span style="font-size: 18px; margin-right: 10px;">📦</span>
                        <div>
                            <div style="font-weight: bold; font-size: 14px;">Track My Order</div>
                            <div style="font-size: 12px; color: #666;">Check order status & shipping</div>
                        </div>
                    </div>
                </button>

                <button onclick="quickHelp('return_item')"
                    style="width: 100%; text-align: left; padding: 12px; border: 1px solid #e0e0e0; border-radius: 6px; background: white; cursor: pointer; margin-bottom: 8px; transition: all 0.2s;">
                    <div style="display: flex; align-items: center;">
                        <span style="font-size: 18px; margin-right: 10px;">↩️</span>
                        <div>
                            <div style="font-weight: bold; font-size: 14px;">Return or Exchange</div>
                            <div style="font-size: 12px; color: #666;">Start a return or exchange</div>
                        </div>
                    </div>
                </button>

                <button onclick="quickHelp('product_info')"
                    style="width: 100%; text-align: left; padding: 12px; border: 1px solid #e0e0e0; border-radius: 6px; background: white; cursor: pointer; margin-bottom: 8px; transition: all 0.2s;">
                    <div style="display: flex; align-items: center;">
                        <span style="font-size: 18px; margin-right: 10px;">💎</span>
                        <div>
                            <div style="font-weight: bold; font-size: 14px;">Product Information</div>
                            <div style="font-size: 12px; color: #666;">Care, authenticity, materials</div>
                        </div>
                    </div>
                </button>

                <button onclick="quickHelp('account_help')"
                    style="width: 100%; text-align: left; padding: 12px; border: 1px solid #e0e0e0; border-radius: 6px; background: white; cursor: pointer; margin-bottom: 8px; transition: all 0.2s;">
                    <div style="display: flex; align-items: center;">
                        <span style="font-size: 18px; margin-right: 10px;">👤</span>
                        <div>
                            <div style="font-weight: bold; font-size: 14px;">Account & Profile</div>
                            <div style="font-size: 12px; color: #666;">Password, settings, profile</div>
                        </div>
                    </div>
                </button>

                <button onclick="createTicket()"
                    style="width: 100%; text-align: left; padding: 12px; border: 1px solid #10b981; border-radius: 6px; background: #10b981; color: white; cursor: pointer; margin-bottom: 15px; transition: all 0.2s;">
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
            <button onclick="openSelfService()"
                style="width: 100%; padding: 10px; background: #f0f0f0; border: 1px solid #ddd; border-radius: 6px; cursor: pointer; margin-bottom: 10px;">
                <span style="font-size: 14px; color: #333;">📚 Browse All Help Topics</span>
            </button>

            <!-- Live Chat Option -->
            <div style="border-top: 1px solid #eee; padding-top: 15px; margin-top: 15px;">
                <p style="font-size: 12px; color: #666; margin: 0 0 10px 0; text-align: center;">Can't find what you
                    need?</p>
                @auth
                    <button onclick="startLiveChat()"
                        style="width: 100%; padding: 12px; background: #2d3748; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
                        💬 Chat with an Agent
                    </button>
                @else
                    <div style="text-align: center;">
                        <a href="{{ route('login') }}"
                            style="display: inline-block; padding: 8px 16px; background: #2d3748; color: white; text-decoration: none; border-radius: 6px; font-size: 12px;">
                            Login for Live Chat
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    @auth
        <!-- Live Chat Box (hidden by default) -->
        <div id="chat-box"
            style="display: none; border: 1px solid #ccc; background-color: white; height: 400px; flex-direction: column; border-radius: 0 0 8px 8px;">
            <!-- Chat Controls -->
            <div
                style="padding: 10px; background: #f8f9fa; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                <button onclick="backToSelfService()"
                    style="background: none; border: none; color: #666; cursor: pointer; font-size: 12px;">
                    ← Back to Help Options
                </button>
                <button id="terminate-chat-btn" onclick="terminateChat()"
                    style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 11px; display: none;">
                    End Chat
                </button>
            </div>

            <!-- Message Display Area -->
            <div id="chat-messages" style="flex-grow: 1; padding: 10px; overflow-y: auto; border-bottom: 1px solid #eee;">
                <p style="text-align: center; color: #999;">Loading chat...</p>
            </div>
            <!-- Message Input Form -->
            <form id="chat-form" style="padding: 10px; display: flex;">
                <input type="text" id="chat-input" placeholder="Type your message..."
                    style="flex-grow: 1; border: 1px solid #ccc; padding: 8px; border-radius: 5px;">
                <button type="submit"
                    style="margin-left: 10px; background-color: #4a5568; color: white; padding: 8px 12px; border: none; border-radius: 5px;">Send</button>
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

        // Ensure all required elements exist before proceeding
        if (!chatToggle || !selfServiceBox) {
            console.error('Chat widget DOM elements not found. Chat functionality may not work.');
            return;
        }

        // Don't auto-load conversation ID - let the server handle this logic
        // Clear any stale conversation ID from localStorage if it exists
        const storedConversationId = localStorage.getItem('activeConversationId');
        if (storedConversationId) {
            // Validate the stored conversation is still active
            fetch(`/chat/conversations/${storedConversationId}`)
                .then(response => response.json())
                .then(conversation => {
                    if (conversation.status !== 'active' || conversation.ended_at) {
                        // Conversation is no longer active, clear it
                        localStorage.removeItem('activeConversationId');
                        console.log('Cleared stale conversation from localStorage:', storedConversationId);
                    }
                })
                .catch(error => {
                    // If we can't validate, clear it to be safe
                    localStorage.removeItem('activeConversationId');
                    console.log('Cleared unvalidatable conversation from localStorage:',
                        storedConversationId);
                });
        }

        window.conversationId = null;
        let isShowingSelfService = false;

        // Check for URL parameter to open specific chat
        const urlParams = new URLSearchParams(window.location.search);
        const openChatId = urlParams.get('open_chat');

        if (openChatId && chatBox && chatMessages) {
            // Auto-open chat widget with specific conversation
            setTimeout(() => {
                startLiveChat(parseInt(openChatId));
            }, 1000);
        }

        // (Step 1) - UI Interaction Layer
        // User clicks chat toggle button to show/hide self-service options    
        chatToggle.addEventListener('click', () => {
            if (!isShowingSelfService) {
                // Show self-service options first
                if (selfServiceBox) selfServiceBox.style.display = 'block';
                if (chatBox) chatBox.style.display = 'none';
                isShowingSelfService = true;
            } else {
                // Hide everything
                if (selfServiceBox) selfServiceBox.style.display = 'none';
                if (chatBox) chatBox.style.display = 'none';
                isShowingSelfService = false;
            }
        });


        // (Step 2) - Chat Initialization Layer
        // User clicks "Chat with an Agent" button
        // Function to initialize chat (get or create conversation)
        window.initializeChat = async function initializeChat() {
            try {
                // (Step 2.1) - HTTP Request to Backend
                // Frontend calls /chat/start endpoint to create/get conversation
                // This is a placeholder route. We will create it next.
                const response = await fetch('/chat/start', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content')
                    }
                });
                const data = await response.json();
                window.conversationId = data.id;


                // (Step 2.2) - Message Fetching Layer
                // Fetch existing messages
                fetchMessages();

                // (Step 2.3) - Real-time Setup Layer
                // Frontend sets up WebSocket connection for real-time messaging
                // Listen for new messages on the private channel
                window.listenForMessages();
            } catch (error) {
                chatMessages.innerHTML = '<p style="color: red;">Could not start chat.</p>';
            }
        }

        // (Step 3) - Queue Management Layer
        // Enhanced chat initialization with queue system
        async function startQueueChat() {
            const chatMessages = document.getElementById('chat-messages');

            // Safety check
            if (!chatMessages) {
                console.error('Chat messages container not found. Retrying...');
                setTimeout(() => startQueueChat(), 100);
                return;
            }

            // Show connecting message
            chatMessages.innerHTML = `
            <div style="text-align: center; padding: 20px; color: #2563eb;">
                <div style="font-size: 18px; margin-bottom: 10px;">🔄 Starting Chat</div>
                <div>Checking for existing conversations...</div>
            </div>
        `;

            try {
                // (Step 3.1) - Queue API Call
                // Frontend calls /chat/start with queue context
                const response = await fetch('/chat/start', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        escalation_context: null,
                        initial_message: 'Hello, I need assistance.'
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();
                console.log('Chat start response:', data);

                // Set the conversation ID
                window.conversationId = data.conversation_id;
                localStorage.setItem('activeConversationId', data.conversation_id);

                // (Step 3.2) - Queue Status Handling
                // Handle different queue scenarios
                // Handle different response scenarios
                if (data.existing_conversation && data.status === 'active' && !data.ended_at && data
                    .assigned_agent_id) {
                    // User has an existing truly active conversation with agent - show it immediately
                    chatMessages.innerHTML = `
                    <div style="text-align: center; padding: 20px; color: #059669;">
                        <div style="font-size: 18px; margin-bottom: 10px;">✅ Active Conversation Found</div>
                        <div>Reconnecting to your agent...</div>
                    </div>
                `;

                    showTerminateButton();

                    setTimeout(() => {
                        window.fetchMessages();
                        window.listenForMessages();
                    }, 1000);

                } else if (data.existing_conversation && data.status === 'active' && !data.ended_at && !data
                    .assigned_agent_id) {
                    // Conversation exists but no agent - show queue status
                    if (data.existing_queue && data.queue_status) {
                        chatMessages.innerHTML = `
                        <div style="text-align: center; padding: 20px; color: #2563eb;">
                            <div style="font-size: 18px; margin-bottom: 10px;">🎫 Already in Queue</div>
                            <div>You were already waiting. Position: #${data.queue_status.position}</div>
                        </div>
                    `;
                        showQueueStatus(data.queue_status);
                        startQueuePolling();
                    } else {
                        // No queue entry, start fresh queue
                        showQueueStatus(data.queue_status);
                        startQueuePolling();
                    }

                } else if (data.existing_conversation && (data.status === 'completed' || data.status ===
                        'abandoned' || data.ended_at)) {
                    // Existing conversation is terminated - clear it and start fresh
                    localStorage.removeItem('activeConversationId');
                    window.conversationId = null;

                    chatMessages.innerHTML = `
                    <div style="text-align: center; padding: 20px; color: #6b7280;">
                        <div style="font-size: 18px; margin-bottom: 10px;">📋 Previous Chat Ended</div>
                        <div>Your previous conversation was ${data.status}. Starting a new chat...</div>
                    </div>
                `;

                    // Start a new conversation after a brief delay
                    setTimeout(() => {
                        startQueueChat();
                    }, 2000);

                } else if (data.queue_status && data.queue_status.in_queue) {
                    // New queue entry - show queue status
                    showQueueStatus(data.queue_status);
                    startQueuePolling();

                } else {
                    // Something went wrong or unexpected response
                    throw new Error('Unexpected response format: ' + JSON.stringify(data));
                }

            } catch (error) {
                console.error('Error starting queue chat:', error);
                chatMessages.innerHTML = `
                <div style="text-align: center; padding: 20px; color: #ef4444;">
                    <div style="font-size: 18px; margin-bottom: 10px;">❌ Connection Failed</div>
                    <div>Could not connect to chat system: ${error.message}</div>
                    <button onclick="startQueueChat()" style="background: #2563eb; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; margin-top: 10px;">
                        Try Again
                    </button>
                </div>
            `;
            }
        }

        //done
        // (Step 4) - Queue Status Display Layer
        function showQueueStatus(queueStatus) {
            if (queueStatus.in_queue) {
                // Show queue status but allow messaging
                const queueStatusDiv = `
                <div id="queue-status" style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 15px; margin-bottom: 15px;">
                                <div style="display: flex; flex-direction: column; align-items: center; margin-top: 10px;">
                    <div style="font-size: 16px; margin-bottom: 8px; color: #1d4ed8;">
                        🎫 You're in the queue
                    </div>
                    <div style="font-size: 20px; font-weight: bold; color: #2563eb;">
                        Position: #${queueStatus.position}
                    </div>
                    <div style="margin: 8px 0; color: #6b7280; font-size: 14px;">
                        Estimated wait: ${queueStatus.estimated_wait} minutes
                    </div>
                    <div style="margin-top: 10px;">
                        <button onclick="leaveQueue()" 
                                style="background: #ef4444; color: white; border: none; 
                                    padding: 6px 12px; border-radius: 4px; 
                                    cursor: pointer; font-size: 12px;">
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
                setTimeout(() => {
                    window.fetchMessages();
                    window.listenForMessages();
                }, 1000);
            } else {
                // Chat is already assigned or completed - remove loading message immediately
                chatMessages.innerHTML = `
                <div style="text-align: center; padding: 20px; color: #059669;">
                    <div style="font-size: 18px; margin-bottom: 10px;">✅ Connected to Agent</div>
                    <div>You can now start chatting!</div>
                </div>
            `;

                // Show terminate button when connected to agent
                showTerminateButton();

                // Load messages and listen for new ones
                setTimeout(() => {
                    window.fetchMessages();
                    window.listenForMessages();
                }, 1000);
            }
        }

        //done
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

        //done
        //(Step 5) - Queue Polling Layer
        // Polls queue status every 5 seconds to check for agent assignment
        function startQueuePolling() {
            // Clear any existing interval
            if (window.queueCheckInterval) {
                clearInterval(window.queueCheckInterval);
            }

            window.queueCheckInterval = setInterval(async () => {
                if (!window.conversationId) {
                    clearInterval(window.queueCheckInterval);
                    return;
                }

                try {
                    // (Step 5.1) - Queue Status API Call
                    // Frontend polls /chat/queue-status/{id} to check queue position
                    const response = await fetch(`/chat/queue-status/${window.conversationId}`);
                    const queueStatus = await response.json();

                    // If user manually left queue, don't process queue updates
                    const chatMessages = document.getElementById('chat-messages');
                    if (chatMessages && chatMessages.innerHTML.includes('Left Queue')) {
                        clearInterval(window.queueCheckInterval);
                        return;
                    }

                    if (!queueStatus.in_queue) {
                        // (Step 5.2) - Agent Assignment Detection
                        // Chat has been assigned to an agent
                        clearInterval(window.queueCheckInterval);

                        const chatMessages = document.getElementById('chat-messages');
                        if (chatMessages) {
                            chatMessages.innerHTML = `
                            <div style="text-align: center; padding: 20px; color: #059669;">
                                <div style="font-size: 18px; margin-bottom: 10px;">✅ Connected to Agent</div>
                                <div>An agent has joined the conversation!</div>
                            </div>
                        `;
                        }

                        // Show terminate button when connected to agent
                        showTerminateButton();

                        // (Step 5.3) - Real-time Setup After Assignment
                        // Load chat interface and setup WebSocket connection
                        setTimeout(() => {
                            window.fetchMessages();
                            window.listenForMessages();
                        }, 2000);
                    } else {
                        // Update queue position if still in queue
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

        //done
        async function leaveQueue() {
            if (!window.conversationId) return;

            if (confirm('Are you sure you want to leave the queue?')) {
                try {
                    // (Step 5.4) - Leave Queue API Call
                    // Frontend calls /chat/leave-queue/{id} to abandon queue
                    const response = await fetch(`/chat/leave-queue/${window.conversationId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Clear polling interval
                        clearInterval(window.queueCheckInterval);

                        // (Step 5.5) - Cleanup After Leaving Queue 
                        // Clean up Echo channel and intervals
                        if (window.currentEchoChannel) {
                            console.log('🔄 Unsubscribing from channel on leave queue');
                            window.Echo.leaveChannel(window.currentEchoChannel);
                            window.currentEchoChannel = null;
                        }
                        if (window.messageRefreshInterval) {
                            clearInterval(window.messageRefreshInterval);
                            window.messageRefreshInterval = null;
                        }

                        // Show left queue message
                        const chatMessages = document.getElementById('chat-messages');
                        if (chatMessages) {
                            chatMessages.innerHTML = `
                            <div style="text-align: center; padding: 20px; color: #6b7280;">
                                <div style="font-size: 18px; margin-bottom: 10px;">👋 Left Queue</div>
                                <div>You have left the chat queue successfully.</div>
                                <button onclick="startQueueChat()" style="background: #2563eb; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; margin-top: 10px;">
                                    Join Queue Again
                                </button>
                            </div>
                        `;
                        }

                        // Clear stored conversation data
                        window.conversationId = null;
                        localStorage.removeItem('activeConversationId');

                        // Hide terminate button if visible
                        hideTerminateButton();

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


        //done
        // (Step 6) (step 5.3.1) - Message Fetching Layer
        // Fetches existing messages from backend API
        // Function to fetch messages - moved to global scope
        window.fetchMessages = async function fetchMessages() {
            if (!window.conversationId) {
                console.log('No conversation ID for fetching messages');
                return;
            }

            const chatMessages = document.getElementById('chat-messages');
            if (!chatMessages) {
                console.error('Chat messages container not found for fetchMessages');
                return;
            }

            try {
                console.log('Fetching messages for conversation:', window.conversationId);
                // (Step 6.1) - Messages API Call
                // Frontend calls /chat/conversations/{id}/messages to get message history
                const response = await fetch(`/chat/conversations/${window.conversationId}/messages`);

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const messages = await response.json();
                console.log('Fetched messages:', messages.length);

                // Clear loading message only if we have the container
                if (chatMessages) {
                    // Check if we're in queue mode and clear appropriately
                    const queueMessagesContainer = document.getElementById('queue-messages');
                    if (queueMessagesContainer) {
                        queueMessagesContainer.innerHTML = '';
                    } else {
                        chatMessages.innerHTML = '';
                    }

                    messages.forEach(message => window.addMessageToBox(message));

                    // Scroll to bottom after loading messages
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            } catch (error) {
                console.error('Failed to fetch messages:', error);
                if (chatMessages) {
                    chatMessages.innerHTML =
                        `<p style="color: red;">Failed to load messages: ${error.message}</p>`;
                }
            }
        };

        //done
        // (Step 7) - Message Rendering Layer
        // Adds a message to the chat UI
        // Function to add a message to the chat box - moved to global scope
        window.addMessageToBox = function addMessageToBox(message) {
            const chatMessages = document.getElementById('chat-messages');
            if (!chatMessages) {
                console.error('Chat messages container not found for addMessageToBox');
                return;
            }

            const messageElement = document.createElement('div');
            const isMe = message.user && message.user.id === {!! json_encode(auth()->id()) !!};
            messageElement.style.marginBottom = '10px';
            messageElement.style.textAlign = isMe ? 'right' : 'left';

            // Handle both 'body' and 'content' fields
            const messageContent = message.body || message.content || '';

            messageElement.innerHTML = `
            <div style="display: inline-block; padding: 8px 12px; border-radius: 10px; background-color: ${isMe ? '#3182ce' : '#e2e8f0'}; color: ${isMe ? 'white' : 'black'};">
                <strong>${isMe ? 'You' : (message.user ? message.user.name : 'System')}:</strong>
                <p>${messageContent}</p>
            </div>
        `;
            // Check if we're in queue mode and append to the correct container
            const queueMessagesContainer = document.getElementById('queue-messages');
            const targetContainer = queueMessagesContainer || chatMessages;

            if (targetContainer) {
                targetContainer.appendChild(messageElement);

                // Scroll the main chat container to bottom
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        }

        //done
        // (Step 8) - Message Sending Layer
        // Handles form submission to send new messages
        // Function to handle form submission
        if (chatForm && chatInput) {
            chatForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                if (!window.conversationId || chatInput.value.trim() === '') return;

                // Check if input is disabled (conversation terminated)
                if (chatInput.disabled) {
                    alert('Cannot send message - conversation has been terminated');
                    return;
                }

                const messageText = chatInput.value.trim();
                chatInput.value = ''; // Clear input immediately

                try {
                    // (Step 8.1) - Send Message API Call
                    // Frontend calls /chat/messages to send new message to backend
                    const response = await fetch('/chat/messages', { //line 38
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            conversation_id: window.conversationId,
                            body: messageText
                        })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        // (Step 8.2) - Optimistic UI Update
                        // Add the message to the chat immediately with correct structure
                        addMessageToBox({
                            id: data.id,
                            body: data.body, // Use 'body' field from response
                            content: data.body, // Also set content for compatibility
                            user: data.user // Use the user object from response
                        });
                    } else {
                        console.error('Failed to send message:', data.message);
                        if (data.message && data.message.includes('terminated')) {
                            // Conversation was terminated - disable interface
                            handleConversationTerminated({
                                conversation_id: window.conversationId,
                                terminatedBy: 'unknown',
                                message: 'Conversation was terminated'
                            }, 'unknown');
                        } else {
                            chatInput.value =
                            messageText; // Restore message if failed for other reasons
                        }
                        alert('Failed to send message: ' + (data.message || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Failed to send message:', error);
                    chatInput.value = messageText; // Restore message if failed
                    alert('Failed to send message');
                }
            });
        }


        //done
        // (Step 9) - Real-time Connection Layer
        // Sets up WebSocket connection for real-time messaging
        // Function to listen for real-time messages using direct Echo - moved to global scope
        window.listenForMessages = function listenForMessages() {
            // check whether exist a valid conversation
            if (!window.conversationId) {
                console.log('❌ No conversation ID available for listening');
                return;
            }
            // check whether the Laravel Echo exist or not 
            if (!window.Echo) {
                console.error('❌ Echo not available - real-time messaging disabled');
                return;
            }

            console.log('🎧 Setting up Echo message listening for conversation:', window.conversationId);

            // Clear any existing fallback polling first
            if (window.messageRefreshInterval) {
                console.log('🔄 Clearing existing fallback polling before setting up real-time');
                clearInterval(window.messageRefreshInterval);
                window.messageRefreshInterval = null;
            }

            // Clear message duplication tracking for new conversation
            if (window.receivedMessageIds) {
                console.log('🔄 Clearing received message IDs for new conversation');
                window.receivedMessageIds.clear();
            }

            // Unsubscribe from any existing channels first
            if (window.currentEchoChannel) {
                console.log('🔄 Unsubscribing from previous channel:', window.currentEchoChannel);
                try {
                    window.Echo.leaveChannel(window.currentEchoChannel);
                } catch (error) {
                    console.log('⚠️ Error leaving channel:', error);
                }
            }

            // (Step 9.2) - Channel Subscription
            // Subscribe to the conversation channel
            const channelName =
            `conversation.${window.conversationId}`; // create the channelName based on conversation_id
            window.currentEchoChannel = channelName;

            console.log('📡 Attempting to subscribe to channel:', channelName);

            //在执行这行代码时，Laravel Echo 会自动向你的 Laravel 后端服务器发送一个认证请求，请求的地址通常是 /broadcasting/auth
            // use Echo subscribe a private channel where other authenticated user can listen to it 
            const channel = window.Echo.private(channelName);

            channel.subscribed(() => {
                console.log('✅ Successfully subscribed to channel:', channelName);
                console.log('🔧 Echo connector state:', window.Echo.connector.pusher.connection
                    .state);
                console.log('🔧 Channel subscription:', channel.subscription);

                // Don't start fallback refresh - rely on real-time only
                // Remove any existing fallback refresh since real-time should work
                if (window.messageRefreshInterval) {
                    clearInterval(window.messageRefreshInterval);
                    window.messageRefreshInterval = null;
                    console.log(
                    '🎯 [CUSTOMER] Cleared fallback refresh on successful subscription');
                }
            })

            // (Step 9.3) - Message Reception Handler
            // FIXED: Use direct Pusher binding like admin side - Echo .listen() stopped working
            if (channel.subscription) {
                console.log('🔗 Setting up direct Pusher bindings for channel subscription');

                // Ensure no fallback polling is running when real-time works
                if (window.messageRefreshInterval) {
                    console.log('🛑 Clearing fallback polling - real-time is working');
                    clearInterval(window.messageRefreshInterval);
                    window.messageRefreshInterval = null;
                }

                // Track received messages to prevent duplicates （if not process will process it )
                if (!window.receivedMessageIds) {
                    window.receivedMessageIds = new Set();
                }


                // Listen for new messages(MessageSent) if the backend server broadcast this event the code inside will execute using direct Pusher bind
                channel.subscription.bind('MessageSent', (data) => {
                    console.log('🎉 [CUSTOMER] Real-time message received via direct Pusher bind');
                    console.log('📨 [CUSTOMER] Message data:', data);

                    const currentUserId = {!! json_encode(auth()->id()) !!};
                    console.log('👤 Current user ID:', currentUserId);

                    if (data.message) {
                        // Check for duplicate messages (if already have message id skip it)
                        if (window.receivedMessageIds.has(data.message.id)) {
                            console.log('🔄 [CUSTOMER] Skipping duplicate message ID:', data.message
                                .id);
                            return;
                        }

                        // Mark message as received
                        window.receivedMessageIds.add(data.message.id);

                        console.log('📝 Message details:', {
                            messageId: data.message.id,
                            messageUserId: data.message.user ? data.message.user.id :
                                'no user',
                            messageType: data.message.message_type || 'normal',
                            messageBody: data.message.body,
                            isFromCurrentUser: data.message.user ? (data.message.user.id ===
                                currentUserId) : false,
                            userObject: data.message.user
                        });

                        if (data.message.user && data.message.user.id !== currentUserId) {
                            console.log(
                                '✅ [CUSTOMER] Adding agent message to chat - INSTANT DELIVERY');
                            console.log('🔄 [CUSTOMER] Message user ID:', data.message.user.id,
                                'vs Current user ID:', currentUserId);

                            // Add message immediately without any delays
                            window.addMessageToBox(data.message);

                            // Force scroll to bottom immediately
                            const chatMessages = document.getElementById('chat-messages');
                            if (chatMessages) {
                                setTimeout(() => {
                                    chatMessages.scrollTop = chatMessages.scrollHeight;
                                }, 50);
                            }

                        } else if (data.message.message_type === 'system') {
                            console.log('✅ [CUSTOMER] Adding system message to chat');
                            addSystemMessageToBox(data.message);
                        } else {
                            console.log('🔄 [CUSTOMER] Ignoring own message or empty message');
                            console.log('🔄 [CUSTOMER] Reason - User match:', data.message.user ? (
                                data.message.user.id === currentUserId) : 'no user object');
                        }
                    } else {
                        console.log('⚠️ [CUSTOMER] No message in event:', data);
                    }
                });

                // (Step 9.6) - Conversation Termination Handler
                // Listen for conversation termination using direct Pusher bind (listen for ConversationTerminated event)
                channel.subscription.bind('ConversationTerminated', (data) => {
                    console.log('🚫 [CUSTOMER] Received ConversationTerminated event:', data);

                    // Handle termination from any source (admin or customer)
                    handleConversationTerminated(data, data.terminatedBy || 'unknown');
                });
            }
            // (Step 9.7) - Error Handling and Fallback      
            channel.error((error) => {
                console.error('❌ [CUSTOMER] Echo channel error:', error);

                // Start fallback polling if Echo fails
                if (!window.messageRefreshInterval) {
                    console.log('🔄 [CUSTOMER] Starting fallback polling due to Echo error');
                    window.messageRefreshInterval = setInterval(() => {
                        if (window.conversationId) {
                            window.fetchMessages();
                        }
                    }, 3000); // Poll every 3 seconds
                }

                // Try to resubscribe immediately if there's an error
                setTimeout(() => {
                    console.log('🔄 [CUSTOMER] Retrying Echo subscription after error');
                    window.listenForMessages();
                }, 2000);
            });

            console.log('🎯 [CUSTOMER] Echo listener setup complete for:', channelName);
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
            window.location.href = '{{ route('login') }}';
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
    window.currentEchoChannel = null; // Track current Echo channel for cleanup
    window.messageRefreshInterval = null; // Fallback message refresh interval


    //done
    //(Step 2)
    function startLiveChat(existingConversationId = null) {
        const selfServiceBox = document.getElementById('self-service-box');
        const chatBox = document.getElementById('chat-box');
        const chatMessages = document.getElementById('chat-messages');

        // Safety check - ensure chat widget is loaded
        if (!chatBox || !chatMessages) {
            console.error('Chat widget not fully loaded. Retrying...');
            setTimeout(() => startLiveChat(existingConversationId), 100);
            return;
        }

        if (selfServiceBox) selfServiceBox.style.display = 'none';
        if (chatBox) chatBox.style.display = 'flex';

        // Always start with queue-based chat instead of trying to resume old conversations
        // The /chat/start endpoint will handle checking for existing active conversations
        startQueueChat();
    }

    async function resumeExistingChat(conversationId) {
        const chatMessages = document.getElementById('chat-messages');

        // Safety check
        if (!chatMessages) {
            console.error('Chat messages container not found. Retrying...');
            setTimeout(() => resumeExistingChat(conversationId), 100);
            return;
        }

        try {
            window.conversationId = conversationId;
            localStorage.setItem('activeConversationId', conversationId);

            // Show resuming message
            chatMessages.innerHTML = `
            <div style="text-align: center; padding: 20px; color: #059669;">
                <div style="font-size: 18px; margin-bottom: 10px;">🔄 Resuming Chat</div>
                <div>Loading your conversation history...</div>
            </div>
        `;

            // Check conversation status first
            const statusResponse = await fetch(`/chat/conversations/${conversationId}`);
            if (!statusResponse.ok) {
                throw new Error('Failed to get conversation status');
            }

            const conversation = await statusResponse.json();
            console.log('Conversation status:', conversation);

            // Check if conversation is still active and has an agent
            if (conversation.status === 'active' && conversation.assigned_agent_id) {
                // Show terminate button for active conversation
                showTerminateButton();

                // Clear the resuming message and load messages immediately
                chatMessages.innerHTML = '';

                // Load existing messages
                await window.fetchMessages();

                // Start listening for new messages
                window.listenForMessages();

                console.log('✅ Resumed active conversation successfully');

            } else if (conversation.status === 'active' && !conversation.assigned_agent_id) {
                // Conversation is active but no agent assigned - show waiting state
                chatMessages.innerHTML = `
                <div style="text-align: center; padding: 20px; color: #f59e0b;">
                    <div style="font-size: 18px; margin-bottom: 10px;">⏳ Waiting for Agent</div>
                    <div>Your conversation is in queue. Please wait for an agent to join.</div>
                </div>
            `;

            } else {
                // Conversation is terminated or completed
                chatMessages.innerHTML = `
                <div style="text-align: center; padding: 20px; color: #6b7280;">
                    <div style="font-size: 18px; margin-bottom: 10px;">📋 Conversation Ended</div>
                    <div>This conversation has been ${conversation.status}.</div>
                    <button onclick="startQueueChat()" style="background: #2563eb; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; margin-top: 10px;">
                        Start New Chat
                    </button>
                </div>
            `;

                // Clear stored conversation data for ended chats
                localStorage.removeItem('activeConversationId');
                window.conversationId = null;
            }

        } catch (error) {
            console.error('Error resuming chat:', error);
            if (chatMessages) {
                chatMessages.innerHTML = `
                <div style="text-align: center; padding: 20px; color: #ef4444;">
                    <div style="font-size: 18px; margin-bottom: 10px;">❌ Connection Error</div>
                    <div>Could not resume conversation: ${error.message}</div>
                    <button onclick="startQueueChat()" style="background: #2563eb; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; margin-top: 10px;">
                        Start New Chat
                    </button>
                </div>
            `;
            }
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
            // Immediately disable the input to prevent further messages
            const chatInput = document.getElementById('chat-input');
            const chatForm = document.getElementById('chat-form');
            const terminateBtn = document.getElementById('terminate-chat-btn');

            if (chatInput) {
                chatInput.disabled = true;
                chatInput.placeholder = 'Terminating chat...';
            }
            if (chatForm) {
                chatForm.style.opacity = '0.5';
                chatForm.style.pointerEvents = 'none';
            }
            if (terminateBtn) {
                terminateBtn.disabled = true;
                terminateBtn.textContent = 'Terminating...';
            }

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
                        console.log('✅ [CUSTOMER] Chat terminated successfully');

                        // Clean up Echo channel and intervals
                        if (window.currentEchoChannel) {
                            console.log('🔄 Unsubscribing from channel on terminate chat');
                            window.Echo.leaveChannel(window.currentEchoChannel);
                            window.currentEchoChannel = null;
                        }
                        if (window.messageRefreshInterval) {
                            clearInterval(window.messageRefreshInterval);
                            window.messageRefreshInterval = null;
                        }

                        // Hide terminate button
                        if (terminateBtn) terminateBtn.style.display = 'none';

                        // Keep input disabled
                        if (chatInput) {
                            chatInput.placeholder = 'Chat has been terminated';
                        }

                        // Show termination message
                        const chatMessages = document.getElementById('chat-messages');
                        if (chatMessages) {
                            const terminationMessage = document.createElement('div');
                            terminationMessage.style.cssText =
                                'text-align: center; color: #666; font-style: italic; margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px;';
                            terminationMessage.textContent =
                                'Chat has been terminated by you. Thank you for contacting us!';
                            chatMessages.appendChild(terminationMessage);
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                        }

                        // Clear the stored conversation ID - IMPORTANT!
                        localStorage.removeItem('activeConversationId');
                        window.conversationId = null;

                        alert('Chat terminated successfully');
                    } else {
                        // Re-enable input if termination failed
                        if (chatInput) {
                            chatInput.disabled = false;
                            chatInput.placeholder = 'Type your message...';
                        }
                        if (chatForm) {
                            chatForm.style.opacity = '1';
                            chatForm.style.pointerEvents = 'auto';
                        }
                        if (terminateBtn) {
                            terminateBtn.disabled = false;
                            terminateBtn.textContent = 'End Chat';
                        }

                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);

                    // Re-enable input if termination failed
                    if (chatInput) {
                        chatInput.disabled = false;
                        chatInput.placeholder = 'Type your message...';
                    }
                    if (chatForm) {
                        chatForm.style.opacity = '1';
                        chatForm.style.pointerEvents = 'auto';
                    }
                    if (terminateBtn) {
                        terminateBtn.disabled = false;
                        terminateBtn.textContent = 'End Chat';
                    }

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
        messageElement.style.cssText =
            'text-align: center; color: #666; font-style: italic; margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px; border: 1px solid #e9ecef;';
        messageElement.innerHTML = `
        <div style="font-size: 12px; color: #999; margin-bottom: 5px;">System Message</div>
        <div>${message.body}</div>
        <div style="font-size: 11px; color: #999; margin-top: 5px;">${new Date(message.created_at).toLocaleString()}</div>
    `;

        chatMessages.appendChild(messageElement);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    //done
    function handleConversationTerminated(data, terminatedBy) {
        console.log('🚫 Handling conversation termination:', data);

        // Immediately disable input to prevent further messages
        const chatInput = document.getElementById('chat-input');
        const chatForm = document.getElementById('chat-form');
        if (chatInput) {
            chatInput.disabled = true;
            chatInput.style.backgroundColor = '#f3f4f6';
            chatInput.placeholder = `Chat terminated by ${terminatedBy === 'admin' ? 'agent' : 'customer'}`;
        }
        if (chatForm) {
            chatForm.style.opacity = '0.5';
            chatForm.style.pointerEvents = 'none';
        }

        // Hide terminate button immediately
        hideTerminateButton();

        // Clean up Echo channels and intervals immediately
        if (window.currentEchoChannel) {
            console.log('🔄 Cleaning up Echo channel on termination');
            try {
                window.Echo.leaveChannel(window.currentEchoChannel);
            } catch (error) {
                console.log('Error leaving channel:', error);
            }
            window.currentEchoChannel = null;
        }
        if (window.messageRefreshInterval) {
            clearInterval(window.messageRefreshInterval);
            window.messageRefreshInterval = null;
        }
        if (window.queueCheckInterval) {
            clearInterval(window.queueCheckInterval);
            window.queueCheckInterval = null;
        }

        // Clear stored conversation ID - IMPORTANT!
        localStorage.removeItem('activeConversationId');
        window.conversationId = null;

        // Show prominent termination notification that overlays the chat
        const chatMessages = document.getElementById('chat-messages');
        if (chatMessages) {
            const terminationOverlay = document.createElement('div');
            terminationOverlay.id = 'termination-overlay';
            terminationOverlay.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(239, 68, 68, 0.1);
            backdrop-filter: blur(2px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        `;
            terminationOverlay.innerHTML = `
            <div style="background: white; border: 2px solid #ef4444; border-radius: 12px; padding: 24px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.1); max-width: 300px;display:flex;flex-direction:column;justify-content:center">
                <div style="font-size: 24px; margin-bottom: 12px;">🚫</div>
                <div style="font-size: 18px; font-weight: bold; color: #dc2626; margin-bottom: 8px;">
                    Chat Terminated
                </div>
                <div style="font-size: 14px; color: #7f1d1d; margin-bottom: 16px;">
                    This conversation was ended by the ${terminatedBy === 'admin' ? 'agent' : 'customer'}.
                </div>
                <div style="font-size: 12px; color: #991b1b;">
                    Thank you for contacting us!
                </div>
                <button onclick="closeTerminationOverlay()" style="background: #dc2626; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; margin-top: 12px; font-size: 12px;">
                    Close
                </button>
            </div>
        `;

            // Position relative to chat container
            const chatBox = document.getElementById('chat-box');
            if (chatBox) {
                chatBox.style.position = 'relative';
                chatBox.appendChild(terminationOverlay);
            }
        }
    }

    //done
    function closeTerminationOverlay() {
        const overlay = document.getElementById('termination-overlay');
        if (overlay) {
            overlay.remove();
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
