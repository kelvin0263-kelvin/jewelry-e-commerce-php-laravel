import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

// Configure Echo with Reverb for real-time chat
const reverbKey = import.meta.env.VITE_REVERB_APP_KEY || 'reverb-key';
const reverbHost = import.meta.env.VITE_REVERB_HOST || window.location.hostname;
const reverbPort = Number(import.meta.env.VITE_REVERB_PORT || 8081);
const reverbScheme = import.meta.env.VITE_REVERB_SCHEME || 'http';
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const xsrfToken = decodeURIComponent(
    document.cookie
        .split('; ')
        .find((row) => row.startsWith('XSRF-TOKEN='))
        ?.split('=')[1] || ''
);

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: reverbKey,
    wsHost: reverbHost,
    wsPort: reverbPort,
    wssPort: reverbPort,
    forceTLS: reverbScheme === 'https',
    enabledTransports: ['ws', 'wss'],
    withCredentials: true,
    // Add error handling
    auth: {
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-XSRF-TOKEN': xsrfToken,
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
    wsHost: reverbHost,
    wsPort: reverbPort,
    authEndpoint: '/broadcasting/auth'
});
