import { defineConfig } from 'vite';
import crypto, { createHash } from 'node:crypto';

// Polyfill for @vitejs/plugin-vue on Node 20+ where globalThis.crypto is WebCrypto without hash().
// The plugin (<=6.0) calls crypto.hash(algo, data) synchronously. Provide a lightweight shim.
if (typeof globalThis.crypto === 'object' && typeof globalThis.crypto.hash !== 'function') {
    const ensureHash = () => {
        if (!crypto.hash) {
            crypto.hash = (algo, data) => {
                const h = createHash(algo);
                h.update(data);
                return h.digest('hex');
            };
        }
    };
    ensureHash();

    globalThis.crypto.hash = (algo, data) => {
        const h = createHash(algo);
        h.update(data);
        return h.digest('hex');
    };
}

import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    // Base public path when served in production
    // base handled automatically by laravel-vite-plugin using assetUrl below
    
    // Server configuration for development
    plugins: [
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        laravel({
            assetUrl: '/Legal-Case-Mngmnt',
            // The path to your application's "entry points" and the public directory
            input: [
                'resources/css/app.css',
                'resources/js/app.js'
            ],
            // Update the auto-refresh paths to work with your setup
            refresh: [
                'resources/views/**',
                'app/Http/Controllers/**',
                'routes/**',
            ],
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                // No global import; telegram.scss imported explicitly in app.js
            },
        },
    },
    server: {
        hmr: {
            host: 'localhost',
            protocol: 'ws',
        },
        watch: {
            usePolling: true,
        },
    },
    build: {
        outDir: 'public/build',
        assetsDir: 'assets',
        emptyOutDir: true,
        manifest: 'manifest.json',
        rollupOptions: {
            output: {
                entryFileNames: 'assets/[name]-[hash].js',
                chunkFileNames: 'assets/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash][extname]',
                manualChunks: {
                    'vendor': ['vue', 'axios'],
                },
            },
        },
    },
});
