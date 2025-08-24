/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Echo configuration is in echo.js