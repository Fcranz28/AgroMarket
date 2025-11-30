import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/dashboard.css',
                'resources/js/onboarding/farmer.js',
                'resources/js/admin/verify.js',
                'resources/js/admin/dashboard.js',
                'resources/js/layouts/dashboard.js',
                'resources/js/layouts/app.js',
                'resources/js/dashboard/products/form.js',
                'resources/js/dashboard/products/index.js',
                'resources/js/checkout/index.js',
                'resources/js/products/show.js',
                'resources/js/search/index.js',
                'resources/js/about.js',
                'resources/js/farmer/orders.js'
            ],
            refresh: true,
        }),
    ],
});