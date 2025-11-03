import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', // Solo este
                'resources/js/app.js'  // Y solo este
            ],
            refresh: true,
        }),
    ],
});