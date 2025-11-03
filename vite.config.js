import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/css/transactions.css',
                'resources/js/transactions.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        // Optimize chunk size
        chunkSizeWarningLimit: 1000,
        // Enable minification (esbuild is default and faster)
        minify: 'esbuild',
        // Optimize dependencies
        rollupOptions: {
            output: {
                manualChunks: undefined, // Let Vite handle chunking automatically
            },
        },
    },
});
