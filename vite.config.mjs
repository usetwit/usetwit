import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';
import path from 'path';

export default defineConfig({
    plugins: [
        vue(),
        tailwindcss(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/app/home.js',
                'resources/js/app/flash.js',
                'resources/js/app/users-index.js',
                'resources/js/app/users-edit.js',
                'resources/js/app/users-create.js',
                'resources/js/app/calendar-shifts-edit.js',
                'resources/js/app/sales-orders-create.js',
                'resources/js/app/locations-index.js',
                'resources/js/app/locations-edit.js',
                'resources/js/app/boms-edit.js',
                'resources/js/app/boms-index.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
});
