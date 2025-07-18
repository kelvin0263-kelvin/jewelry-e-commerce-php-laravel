/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// --- START: Real-time Chat Configuration ---

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

/**
 * We'll instantiate a new Echo instance to connect to our Reverb server.
 * This allows us to listen for real-time events on the client-side.
 */
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// --- END: Real-time Chat Configuration ---