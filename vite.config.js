import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/dashboard.css',
                'resources/css/login.css',
                'resources/css/contacto.css',
                'resources/css/categorias.css',
                'resources/css/admin/reports.css',
                'resources/css/checkout/addresses.css',
                'resources/css/profile/addresses.css',
                'resources/js/login.js',
                'resources/js/contacto.js',
                'resources/js/categorias.js',
                'resources/js/onboarding/farmer.js',
                'resources/js/admin/verify.js',
                'resources/js/admin/dashboard.js',
                'resources/js/admin/reports.js',
                'resources/js/layouts/dashboard.js',
                'resources/js/layouts/app.js',
                'resources/js/dashboard/products/form.js',
                'resources/js/dashboard/products/index.js',
                'resources/js/checkout/index.js',
                'resources/js/checkout/address-map.js',
                'resources/js/products/show.js',
                'resources/js/profile/addresses.js',
                'resources/js/search/index.js',
                'resources/js/about.js',
                'resources/js/farmer/dashboard.js',
                'resources/js/farmer/orders.js',
            ],
            refresh: true,
        }),
    ],
});
