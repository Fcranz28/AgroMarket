import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/dashboard.css',
                'resources/css/admin/reports.css',
                'resources/js/firebase-auth.js',
                'resources/js/admin/verify.js',
                'resources/js/admin/dashboard.js',
                'resources/js/admin/reports.js',
                'resources/js/layouts/dashboard.js',
                'resources/js/dashboard/products/form.js',
                'resources/js/dashboard/products/index.js',
                'resources/js/farmer/dashboard.js',
                'resources/js/farmer/orders.js',
            ],
            refresh: true,
        }),
    ],
});
