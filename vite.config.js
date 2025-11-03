import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/style.css',
                'resources/css/style2.css',
                'resources/css/login.css',
                'resources/css/contacto.css',
                'resources/css/nosotros.css',
                'resources/css/categorias.css',
                'resources/css/pedidos.css',
                'resources/css/CRUD.css',
                
                'resources/js/app.js',
                'resources/js/script.js',
                'resources/js/carrito.js',
                'resources/js/login.js',
                'resources/js/contacto.js',
                'resources/js/nosotros.js',
                'resources/js/categorias.js',
                'resources/js/pedidos.js',
                'resources/js/CRUD.js'
            ],
            refresh: true,
        }),
    ],
});