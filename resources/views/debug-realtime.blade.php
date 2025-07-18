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
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
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
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="font-semibold text-gray-700">Real-time Messages</h3>
                <div id="realtime-status" class="text-red-500">Not tested</div>
            </div>
        </div>

        <!-- Test Buttons -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold mb-4">Tests</h2>
            <div class="space-x-4 mb-4">
                <button onclick="testAuthentication()" class="bg-yellow-500 text-white px-4 py-2 rounded">Test Authentication</button>
                <button onclick="testEchoConnection()" class="bg-blue-500 text-white px-4 py-2 rounded">Test Echo Connection</button>
                <button onclick="testBroadcastAuth()" class="bg-green-500 text-white px-4 py-2 rounded">Test Broadcast Auth</button>
                <button onclick="testInterceptBroadcastAuth()" class="bg-red-500 text-white px-4 py-2 rounded">Test Intercept Auth</button>
                <button onclick="testManualChannelAuth()" class="bg-indigo-500 text-white px-4 py-2 rounded">Test Manual Channel Auth</button>
                <button onclick="testCustomBroadcastAuth()" class="bg-pink-500 text-white px-4 py-2 rounded">Test Custom Auth</button>
                <button onclick="testMessageBroadcast()" class="bg-purple-500 text-white px-4 py-2 rounded">Test Message Broadcast</button>
                <button onclick="testRealTimeConnection()" class="bg-teal-500 text-white px-4 py-2 rounded">Test Real-Time Connection</button>
                <button onclick="testLaravelBroadcast()" class="bg-emerald-500 text-white px-4 py-2 rounded">Test Working Broadcast</button>
                <button onclick="testConnectionStability()" class="bg-cyan-500 text-white px-4 py-2 rounded">Test Connection Stability</button>
                <button onclick="checkLogs()" class="bg-orange-500 text-white px-4 py-2 rounded">Check Logs</button>
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

        // Check Laravel logs
        async function checkLogs() {
            log('Checking Laravel logs...', 'info');
            
            try {
                const response = await fetch('/debug-logs', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    log('✅ Laravel logs retrieved (last 50 lines)', 'success');
                    log('Total log lines: ' + data.total_lines, 'info');
                    
                    // Show recent logs
                    data.recent_logs.forEach(line => {
                        if (line.trim()) {
                            log('LOG: ' + line, 'info');
                        }
                    });
                } else {
                    log('❌ Failed to retrieve logs', 'error');
                }
            } catch (error) {
                log('❌ Error checking logs: ' + error.message, 'error');
            }
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

        // Test intercept broadcast authentication
        async function testInterceptBroadcastAuth() {
            log('Testing intercept broadcast authentication...', 'info');
            
            try {
                const response = await fetch('/debug-intercept-broadcast-auth', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        channel_name: 'private-chat.1',
                        socket_id: '12345.67890'
                    })
                });

                if (response.ok) {
                    const result = await response.json();
                    log('✅ Intercept broadcast auth successful!', 'success');
                    log('Auth result: ' + JSON.stringify(result), 'info');
                } else {
                    const errorText = await response.text();
                    log(`❌ Intercept broadcast auth failed: ${response.status} - ${errorText}`, 'error');
                }
            } catch (error) {
                log(`❌ Intercept broadcast auth error: ${error.message}`, 'error');
            }
        }

        // Test manual channel auth
        async function testManualChannelAuth() {
            log('Testing manual channel authentication...', 'info');
            
            try {
                const response = await fetch('/test-channel-auth', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        test: 'manual_channel_auth'
                    })
                });

                if (response.ok) {
                    const result = await response.json();
                    log('✅ Manual channel auth successful!', 'success');
                    log('Result: ' + JSON.stringify(result), 'info');
                } else {
                    const errorText = await response.text();
                    log(`❌ Manual channel auth failed: ${response.status} - ${errorText}`, 'error');
                }
            } catch (error) {
                log(`❌ Manual channel auth error: ${error.message}`, 'error');
            }
        }

        // Test custom broadcast authentication
        async function testCustomBroadcastAuth() {
            log('Testing custom broadcast authentication (with custom auth route)...', 'info');
            
            try {
                const channelName = 'private-chat.1';
                let socketId = '12345.67890';
                
                // Try to get actual socket ID from Echo connection if available
                if (window.Echo && window.Echo.connector && window.Echo.connector.pusher && window.Echo.connector.pusher.connection) {
                    socketId = window.Echo.connector.pusher.connection.socket_id;
                    log(`Using real socket ID: ${socketId}`, 'info');
                } else {
                    log(`Using fake socket ID: ${socketId}`, 'info');
                }
                
                log(`Testing custom broadcast auth with channel: ${channelName}`, 'info');
                
                const response = await fetch('/broadcasting/auth', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        channel_name: channelName,
                        socket_id: socketId
                    })
                });

                if (response.ok) {
                    const result = await response.json();
                    log('✅ Custom broadcast auth successful!', 'success');
                    log('Auth result: ' + JSON.stringify(result), 'info');
                    updateStatus('auth-status', 'Working', true);
                    
                    // If successful, also test a real Echo channel subscription
                    try {
                        log('Testing real Echo channel subscription...', 'info');
                        window.Echo.private('chat.1')
                            .listen('MessageSent', (e) => {
                                log('✅ Real-time message received!', 'success');
                            });
                        log('✅ Echo channel subscription successful!', 'success');
                    } catch (echoError) {
                        log('❌ Echo channel subscription failed: ' + echoError.message, 'error');
                    }
                } else {
                    const errorText = await response.text();
                    log(`❌ Custom broadcast auth failed: ${response.status} - ${errorText}`, 'error');
                    updateStatus('auth-status', 'Failed', false);
                }
            } catch (error) {
                log(`❌ Custom broadcast auth error: ${error.message}`, 'error');
                updateStatus('auth-status', 'Error', false);
            }
        }

        // Test broadcast authentication
        async function testBroadcastAuth() {
            log('Testing broadcast authentication...', 'info');
            
            // First test our debug route
            try {
                log('Testing debug authentication route...', 'info');
                const debugResponse = await fetch('/debug-broadcast-auth', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        test: 'debug'
                    })
                });

                if (debugResponse.ok) {
                    const debugData = await debugResponse.json();
                    log('✅ Debug auth successful: ' + debugData.message, 'success');
                    log('User: ' + debugData.user.email, 'info');
                } else {
                    const debugError = await debugResponse.json();
                    log(`❌ Debug auth failed: ${debugResponse.status} - ${debugError.error}`, 'error');
                    updateStatus('auth-status', 'Failed', false);
                    return;
                }
            } catch (error) {
                log(`❌ Debug auth error: ${error.message}`, 'error');
                updateStatus('auth-status', 'Error', false);
                return;
            }
            
            // Now test actual broadcast auth with proper channel name format
            try {
                const channelName = 'private-chat.1';
                let socketId = '12345.67890';
                
                // Try to get actual socket ID from Echo connection if available
                if (window.Echo && window.Echo.connector && window.Echo.connector.pusher && window.Echo.connector.pusher.connection) {
                    socketId = window.Echo.connector.pusher.connection.socket_id;
                    log(`Using real socket ID: ${socketId}`, 'info');
                } else {
                    log(`Using fake socket ID: ${socketId}`, 'info');
                }
                
                log(`Testing broadcast auth with channel: ${channelName}`, 'info');
                
                const response = await fetch('/broadcasting/auth', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        channel_name: channelName,
                        socket_id: socketId
                    })
                });

                if (response.ok) {
                    const result = await response.json();
                    log('✅ Broadcast auth successful', 'success');
                    log('Auth result: ' + JSON.stringify(result), 'info');
                    updateStatus('auth-status', 'Working', true);
                } else {
                    const errorText = await response.text();
                    log(`❌ Broadcast auth failed: ${response.status} - ${errorText}`, 'error');
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

        // Test real-time connection end-to-end
        async function testRealTimeConnection() {
            log('Testing complete real-time connection...', 'info');
            
            try {
                // First ensure we have a conversation
                if (!conversationId) {
                    await startConversation();
                }
                
                // Test authentication
                log('Step 1: Testing authentication...', 'info');
                await testAuthentication();
                
                // Test custom broadcast auth
                log('Step 2: Testing custom broadcast auth...', 'info');
                await testCustomBroadcastAuth();
                
                // Test real Echo subscription
                log('Step 3: Testing Echo subscription...', 'info');
                if (window.Echo && conversationId) {
                    try {
                        window.Echo.private(`chat.${conversationId}`)
                            .listen('MessageSent', (e) => {
                                log(`🎉 REAL-TIME MESSAGE RECEIVED: ${e.message.body}`, 'success');
                                log(`From: ${e.message.user.name}`, 'info');
                                
                                // Add to test messages display
                                const messagesDiv = document.getElementById('test-messages');
                                messagesDiv.innerHTML += `<div class="mb-2 p-2 bg-green-100 rounded">
                                    <strong>🎉 REAL-TIME:</strong> ${e.message.body} (from ${e.message.user.name})
                                </div>`;
                                messagesDiv.scrollTop = messagesDiv.scrollHeight;
                            });
                        log('✅ Echo subscription successful!', 'success');
                        
                        // Send a test message to trigger real-time
                        log('Step 4: Sending test message for real-time...', 'info');
                        await sendTestMessage();
                        
                    } catch (error) {
                        log(`❌ Echo subscription failed: ${error.message}`, 'error');
                    }
                } else {
                    log('❌ Echo not available or no conversation ID', 'error');
                }
                
                log('🎯 Real-time connection test completed!', 'success');
                
            } catch (error) {
                log(`❌ Real-time connection test failed: ${error.message}`, 'error');
            }
        }

        // Test Laravel's default broadcast authentication
        async function testLaravelBroadcast() {
            log('Testing custom broadcast system (now on /broadcasting/auth)...', 'info');
            
            try {
                // First ensure we have a conversation
                if (!conversationId) {
                    await startConversation();
                }
                
                // Test Laravel's default broadcast auth
                const channelName = 'private-chat.' + conversationId;
                let socketId = '12345.67890';
                
                // Try to get actual socket ID from Echo connection if available
                if (window.Echo && window.Echo.connector && window.Echo.connector.pusher && window.Echo.connector.pusher.connection) {
                    socketId = window.Echo.connector.pusher.connection.socket_id;
                    log(`Using real socket ID: ${socketId}`, 'info');
                } else {
                    log(`Using fake socket ID: ${socketId}`, 'info');
                }
                
                log(`Testing Laravel broadcast auth with channel: ${channelName}`, 'info');
                
                const response = await fetch('/broadcasting/auth', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        channel_name: channelName,
                        socket_id: socketId
                    })
                });

                if (response.ok) {
                    const result = await response.json();
                    log('✅ Laravel broadcast auth successful!', 'success');
                    log('Auth result: ' + JSON.stringify(result), 'info');
                    
                    // Now test real Echo subscription with proper channel name
                    try {
                        log('Testing Echo subscription with Laravel auth...', 'info');
                        
                                                 // Set up message listener
                         window.Echo.private(`chat.${conversationId}`)
                             .listen('MessageSent', (e) => {
                                 log(`🎉 REAL-TIME MESSAGE RECEIVED: ${e.message.body}`, 'success');
                                 log(`From: ${e.message.user.name}`, 'info');
                                 
                                 // Update real-time status
                                 updateStatus('realtime-status', 'Working!', true);
                                 
                                 // Add to test messages display
                                 const messagesDiv = document.getElementById('test-messages');
                                 messagesDiv.innerHTML += `<div class="mb-2 p-2 bg-green-100 rounded">
                                     <strong>🎉 REAL-TIME:</strong> ${e.message.body} (from ${e.message.user.name})
                                 </div>`;
                                 messagesDiv.scrollTop = messagesDiv.scrollHeight;
                             });
                        
                        log('✅ Echo subscription successful!', 'success');
                        
                        // Send a test message
                        log('Sending test message...', 'info');
                        await sendTestMessage();
                        
                    } catch (echoError) {
                        log('❌ Echo subscription failed: ' + echoError.message, 'error');
                    }
                } else {
                    const errorText = await response.text();
                    log(`❌ Laravel broadcast auth failed: ${response.status} - ${errorText}`, 'error');
                }
            } catch (error) {
                log(`❌ Laravel broadcast test error: ${error.message}`, 'error');
            }
        }

        // Test connection stability
        async function testConnectionStability() {
            log('Testing WebSocket connection stability...', 'info');
            
            let connectionChecks = 0;
            const maxChecks = 10;
            
            const checkConnection = () => {
                connectionChecks++;
                
                if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
                    const state = window.Echo.connector.pusher.connection.state;
                    const socketId = window.Echo.connector.pusher.connection.socket_id;
                    
                    log(`Connection check ${connectionChecks}/${maxChecks}: State = ${state}, Socket ID = ${socketId}`, 'info');
                    
                    if (state === 'connected') {
                        log('✅ Connection stable!', 'success');
                    } else if (state === 'disconnected') {
                        log('❌ Connection lost!', 'error');
                    } else if (state === 'connecting') {
                        log('🔄 Still connecting...', 'info');
                    }
                } else {
                    log('❌ Echo not available!', 'error');
                }
                
                if (connectionChecks < maxChecks) {
                    setTimeout(checkConnection, 2000); // Check every 2 seconds
                } else {
                    log('🎯 Connection stability test completed', 'success');
                }
            };
            
            checkConnection();
        }

        // Test authentication status
        async function testAuthentication() {
            log('Testing authentication status...', 'info');
            
            try {
                const response = await fetch('/debug-auth', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    log('✅ Authentication status: ' + data.status, 'success');
                    if (data.user) {
                        log('User: ' + data.user.email + ' (ID: ' + data.user.id + ')', 'info');
                        log('Session ID: ' + data.session_id, 'info');
                    }
                } else {
                    log('❌ Authentication check failed', 'error');
                }
            } catch (error) {
                log('❌ Authentication check error: ' + error.message, 'error');
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
            let message = input.value.trim();
            
            // If no message is provided, use a default test message
            if (!message) {
                message = 'Test message from real-time connection test';
                log('Using default test message: ' + message, 'info');
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
                    if (input.value.trim()) {
                        input.value = '';
                    }
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