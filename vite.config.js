import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', // CSS principal
                'resources/js/app.js',   // JS principal
                // Añade JS y CSS específicos de páginas si no los importas en app.js/app.css
                'resources/css/login.css',
                'resources/js/login.js',
                'resources/js/categorias.js',
                // etc.
            ],
            refresh: true,
        }),
    ],
});