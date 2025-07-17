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
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    },
    // Add connection timeout
    activityTimeout: 30000,
    pongTimeout: 10000,
    unavailableTimeout: 10000
});

// Add connection event listeners for debugging
window.Echo.connector.pusher.connection.bind('connected', function() {
    console.log('✓ Echo connected to Reverb server');
});

window.Echo.connector.pusher.connection.bind('disconnected', function() {
    console.log('✗ Echo disconnected from Reverb server');
});

window.Echo.connector.pusher.connection.bind('error', function(error) {
    console.error('Echo connection error:', error);
});

// Global error handler
window.Echo.connector.pusher.connection.bind('unavailable', function() {
    console.error('✗ Reverb server is unavailable. Make sure it\'s running on port 8081');
});
