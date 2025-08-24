import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

// Configure Echo with Reverb for real-time chat
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: 'reverb-key',
    wsHost: window.location.hostname,
    wsPort: 8081,
    wssPort: 8081,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
    withCredentials: true,
    // Add error handling
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'X-Requested-With': 'XMLHttpRequest'
        }
    },
    // Add connection timeout
    activityTimeout: 30000,
    pongTimeout: 10000,
    unavailableTimeout: 10000,
    // Add more detailed error handling
    authEndpoint: '/broadcasting/auth',
    enableLogging: true,
    logToConsole: true,
    // Add cluster and other options for compatibility
    cluster: 'mt1',
    encrypted: false
});

// Add connection event listeners with detailed logging
window.Echo.connector.pusher.connection.bind('connected', function() {
    console.log('✅ Echo connected to Reverb server on port 8081');
});

window.Echo.connector.pusher.connection.bind('connecting', function() {
    console.log('🔄 Echo connecting to Reverb server...');
});

window.Echo.connector.pusher.connection.bind('disconnected', function() {
    console.log('❌ Echo disconnected from Reverb server');
});

window.Echo.connector.pusher.connection.bind('error', function(error) {
    console.error('❌ Echo connection error:', error);
});

// Global error handler
window.Echo.connector.pusher.connection.bind('unavailable', function() {
    console.error('❌ Reverb server is unavailable. Make sure it\'s running on port 8081');
});

// Add authentication error handler
window.Echo.connector.pusher.connection.bind('auth_error', function(error) {
    console.error('❌ Echo authentication error:', error);
});

// Log Echo initialization
console.log('🚀 Laravel Echo initialized with Reverb configuration:', {
    broadcaster: 'reverb',
    wsHost: window.location.hostname,
    wsPort: 8081,
    authEndpoint: '/broadcasting/auth'
});
