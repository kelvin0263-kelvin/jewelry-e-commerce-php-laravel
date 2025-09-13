import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: 'localhost',   // 👈 force localhost (no ::1 surprises)
        port: 5173,          // 👈 consistent port
        hmr: {
            host: 'localhost', // 👈 makes Hot Module Reload match
        },
    },
});
