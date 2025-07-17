<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Real-Time Chat Debug</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Real-Time Chat Debug</h1>
        
        <!-- Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="font-semibold text-gray-700">Echo Status</h3>
                <div id="echo-status" class="text-red-500">Checking...</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="font-semibold text-gray-700">WebSocket Connection</h3>
                <div id="websocket-status" class="text-red-500">Checking...</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="font-semibold text-gray-700">Broadcasting Auth</h3>
                <div id="auth-status" class="text-red-500">Checking...</div>
            </div>
        </div>

        <!-- Test Buttons -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold mb-4">Tests</h2>
            <div class="space-x-4">
                <button onclick="testEchoConnection()" class="bg-blue-500 text-white px-4 py-2 rounded">Test Echo Connection</button>
                <button onclick="testBroadcastAuth()" class="bg-green-500 text-white px-4 py-2 rounded">Test Broadcast Auth</button>
                <button onclick="testMessageBroadcast()" class="bg-purple-500 text-white px-4 py-2 rounded">Test Message Broadcast</button>
                <button onclick="clearLogs()" class="bg-gray-500 text-white px-4 py-2 rounded">Clear Logs</button>
            </div>
        </div>

        <!-- Real-Time Test -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold mb-4">Real-Time Message Test</h2>
            <div class="flex space-x-4 mb-4">
                <input type="text" id="test-message" placeholder="Enter test message" class="flex-1 border rounded px-3 py-2">
                <button onclick="sendTestMessage()" class="bg-indigo-500 text-white px-4 py-2 rounded">Send Test Message</button>
            </div>
            <div id="test-messages" class="border rounded p-4 h-32 overflow-y-auto bg-gray-50"></div>
        </div>

        <!-- Debug Logs -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Debug Logs</h2>
            <div id="debug-logs" class="bg-black text-green-400 p-4 rounded font-mono text-sm h-64 overflow-y-auto"></div>
        </div>
    </div>

    <script>
        let conversationId = null;
        let echoConnected = false;

        function log(message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString();
            const logDiv = document.getElementById('debug-logs');
            const color = type === 'error' ? 'text-red-400' : type === 'success' ? 'text-green-400' : 'text-blue-400';
            logDiv.innerHTML += `<div class="${color}">[${timestamp}] ${message}</div>`;
            logDiv.scrollTop = logDiv.scrollHeight;
            console.log(`[${timestamp}] ${message}`);
        }

        function updateStatus(elementId, status, isSuccess) {
            const element = document.getElementById(elementId);
            element.textContent = status;
            element.className = isSuccess ? 'text-green-500' : 'text-red-500';
        }

        function clearLogs() {
            document.getElementById('debug-logs').innerHTML = '';
        }

        // Test Echo connection
        function testEchoConnection() {
            log('Testing Echo connection...', 'info');
            
            if (typeof window.Echo === 'undefined') {
                log('❌ Echo is not loaded!', 'error');
                updateStatus('echo-status', 'Not Loaded', false);
                return;
            }

            log('✅ Echo object exists', 'success');
            updateStatus('echo-status', 'Loaded', true);

            // Test connection state
            if (window.Echo.connector && window.Echo.connector.pusher) {
                const state = window.Echo.connector.pusher.connection.state;
                log(`WebSocket state: ${state}`, 'info');
                updateStatus('websocket-status', state, state === 'connected');
            }
        }

        // Test broadcast authentication
        async function testBroadcastAuth() {
            log('Testing broadcast authentication...', 'info');
            
            try {
                const response = await fetch('/broadcasting/auth', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        channel_name: 'chat.1'
                    })
                });

                if (response.ok) {
                    log('✅ Broadcast auth successful', 'success');
                    updateStatus('auth-status', 'Working', true);
                } else {
                    log(`❌ Broadcast auth failed: ${response.status}`, 'error');
                    updateStatus('auth-status', 'Failed', false);
                }
            } catch (error) {
                log(`❌ Broadcast auth error: ${error.message}`, 'error');
                updateStatus('auth-status', 'Error', false);
            }
        }

        // Test message broadcast
        async function testMessageBroadcast() {
            if (!conversationId) {
                log('No conversation ID. Starting conversation first...', 'info');
                await startConversation();
            }

            log('Testing message broadcast...', 'info');
            
            try {
                const response = await fetch('/admin/chat/messages', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        conversation_id: conversationId,
                        user_id: {{ auth()->id() ?? 1 }},
                        content: 'Test broadcast message'
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    log('✅ Message broadcast successful', 'success');
                    log(`Message ID: ${data.id}`, 'info');
                } else {
                    log(`❌ Message broadcast failed: ${response.status}`, 'error');
                }
            } catch (error) {
                log(`❌ Message broadcast error: ${error.message}`, 'error');
            }
        }

        // Start conversation
        async function startConversation() {
            try {
                const response = await fetch('/chat/start', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    conversationId = data.id;
                    log(`✅ Conversation started with ID: ${conversationId}`, 'success');
                    setupMessageListener();
                } else {
                    log(`❌ Failed to start conversation: ${response.status}`, 'error');
                }
            } catch (error) {
                log(`❌ Conversation start error: ${error.message}`, 'error');
            }
        }

        // Setup message listener
        function setupMessageListener() {
            if (!window.Echo || !conversationId) return;

            log(`Setting up listener for channel: chat.${conversationId}`, 'info');
            
            window.Echo.private(`chat.${conversationId}`)
                .listen('MessageSent', (e) => {
                    log(`📨 Received real-time message: ${e.message.body}`, 'success');
                    const messagesDiv = document.getElementById('test-messages');
                    messagesDiv.innerHTML += `<div class="mb-2 p-2 bg-blue-100 rounded">
                        <strong>${e.message.user.name}:</strong> ${e.message.body}
                    </div>`;
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                })
                .error((error) => {
                    log(`❌ Channel error: ${error}`, 'error');
                });
        }

        // Send test message
        async function sendTestMessage() {
            const input = document.getElementById('test-message');
            const message = input.value.trim();
            
            if (!message) {
                log('Please enter a message', 'error');
                return;
            }

            if (!conversationId) {
                await startConversation();
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
                        user_id: {{ auth()->id() ?? 1 }},
                        content: message
                    })
                });

                if (response.ok) {
                    log(`✅ Test message sent: ${message}`, 'success');
                    input.value = '';
                } else {
                    log(`❌ Failed to send test message: ${response.status}`, 'error');
                }
            } catch (error) {
                log(`❌ Test message error: ${error.message}`, 'error');
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            log('🚀 Debug page loaded', 'info');
            
            // Initial status check
            setTimeout(() => {
                testEchoConnection();
                startConversation();
            }, 1000);

            // Setup Echo connection listeners
            if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
                window.Echo.connector.pusher.connection.bind('connected', function() {
                    log('✅ Echo connected to Reverb server', 'success');
                    updateStatus('websocket-status', 'Connected', true);
                    echoConnected = true;
                });

                window.Echo.connector.pusher.connection.bind('disconnected', function() {
                    log('❌ Echo disconnected from Reverb server', 'error');
                    updateStatus('websocket-status', 'Disconnected', false);
                    echoConnected = false;
                });

                window.Echo.connector.pusher.connection.bind('error', function(error) {
                    log(`❌ Echo connection error: ${JSON.stringify(error)}`, 'error');
                    updateStatus('websocket-status', 'Error', false);
                });

                window.Echo.connector.pusher.connection.bind('unavailable', function() {
                    log('❌ Reverb server unavailable on port 8081', 'error');
                    updateStatus('websocket-status', 'Unavailable', false);
                });
            }
        });
    </script>
</body>
</html> 