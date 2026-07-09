import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    resolve: {
        dedupe: ['react', 'react-dom', 'react-router'],
    },
    server: {
        host: '127.0.0.1',
        port: 5173,
        strictPort: true,
        hmr: {
            host: '127.0.0.1',
        },
    },
    plugins: [
        react(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/crm.css',
                'resources/js/crm/main.jsx',
                'resources/css/admin.css',
                'resources/js/admin/main.jsx',
            ],
            refresh: true,
        }),
    ],
});
