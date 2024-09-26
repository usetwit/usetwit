import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/app/home.js',
                'resources/js/app/navbar.js',
                'resources/js/app/sidebar.js',
                'resources/js/app/users-index.js',
            ],
            refresh: true,
        }),
    ],
});
