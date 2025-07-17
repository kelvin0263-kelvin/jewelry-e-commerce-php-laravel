// Polling-based real-time system (fallback for WebSocket issues)
class PollingRealtime {
    constructor() {
        this.conversations = {};
        this.pollingInterval = 2000; // Poll every 2 seconds
        this.isPolling = false;
        this.lastMessageIds = {};
    }

    // Start polling for a conversation
    startPolling(conversationId) {
        if (this.conversations[conversationId]) {
            return; // Already polling this conversation
        }

        this.conversations[conversationId] = {
            id: conversationId,
            callbacks: [],
            lastCheck: Date.now()
        };

        if (!this.isPolling) {
            this.startPollingLoop();
        }

        console.log(`Started polling for conversation ${conversationId}`);
    }

    // Stop polling for a conversation
    stopPolling(conversationId) {
        delete this.conversations[conversationId];
        
        if (Object.keys(this.conversations).length === 0) {
            this.isPolling = false;
        }

        console.log(`Stopped polling for conversation ${conversationId}`);
    }

    // Add callback for new messages
    onMessage(conversationId, callback) {
        if (!this.conversations[conversationId]) {
            this.startPolling(conversationId);
        }

        this.conversations[conversationId].callbacks.push(callback);
    }

    // Start the polling loop
    startPollingLoop() {
        if (this.isPolling) return;
        
        this.isPolling = true;
        this.poll();
    }

    // Main polling function
    async poll() {
        if (!this.isPolling) return;

        for (const conversationId in this.conversations) {
            try {
                await this.checkForNewMessages(conversationId);
            } catch (error) {
                console.error(`Error polling conversation ${conversationId}:`, error);
            }
        }

        // Continue polling if we still have active conversations
        if (this.isPolling && Object.keys(this.conversations).length > 0) {
            setTimeout(() => this.poll(), this.pollingInterval);
        }
    }

    // Check for new messages in a conversation
    async checkForNewMessages(conversationId) {
        try {
            const response = await fetch(`/admin/chat/conversations/${conversationId}/messages`);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const messages = await response.json();
            const conversation = this.conversations[conversationId];
            
            if (!conversation) return;

            // Find new messages since last check
            const lastMessageId = this.lastMessageIds[conversationId] || 0;
            const newMessages = messages.filter(msg => msg.id > lastMessageId);

            if (newMessages.length > 0) {
                // Update last message ID
                this.lastMessageIds[conversationId] = Math.max(...newMessages.map(msg => msg.id));

                // Call all callbacks for this conversation
                newMessages.forEach(message => {
                    conversation.callbacks.forEach(callback => {
                        try {
                            callback(message);
                        } catch (error) {
                            console.error('Error in message callback:', error);
                        }
                    });
                });

                console.log(`Found ${newMessages.length} new messages in conversation ${conversationId}`);
            }
        } catch (error) {
            console.error(`Failed to check messages for conversation ${conversationId}:`, error);
        }
    }

    // Send a message
    async sendMessage(conversationId, userId, content) {
        try {
            const response = await fetch('/admin/chat/messages', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    conversation_id: conversationId,
                    user_id: userId,
                    content: content
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const message = await response.json();
            
            // Immediately check for new messages to get the sent message
            setTimeout(() => this.checkForNewMessages(conversationId), 100);
            
            return message;
        } catch (error) {
            console.error('Failed to send message:', error);
            throw error;
        }
    }
}

// Create global instance
window.PollingRealtime = new PollingRealtime();

// Provide Echo-like interface for compatibility
window.PollingEcho = {
    private: function(channel) {
        const conversationId = channel.replace('chat.', '');
        return {
            listen: function(event, callback) {
                if (event === 'MessageSent') {
                    window.PollingRealtime.onMessage(conversationId, (message) => {
                        callback({ message: message });
                    });
                }
                return this;
            },
            error: function(callback) {
                // Handle errors if needed
                return this;
            }
        };
    }
};

console.log('Polling-based real-time system loaded'); 