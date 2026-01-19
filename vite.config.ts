import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import Components from 'unplugin-vue-components/vite';
import { VueMapUiResolver, VueMapUiPreset } from 'unplugin-vue-map-ui';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import { VitePWA } from 'vite-plugin-pwa';
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        Components({
            resolvers: [VueMapUiResolver()],
            types: [VueMapUiPreset]
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
        VitePWA({
            outDir: 'public',
            filename: 'sw.js',
            manifestFilename: 'manifest.webmanifest',
            scope: '/',
            base: '/',
            injectRegister: false,
            registerType: 'autoUpdate',
            includeAssets: [
                'favicon.ico',
                'favicon.svg',
                'apple-touch-icon.png',
                'robots.txt',
                'offline.html',
                'icons/*.png',
            ],
            manifest: {
                id: '/',
                name: 'Pixrup',
                short_name: 'Pixrup',
                description: 'AI-powered property appraisal & renovation reports',
                start_url: '/?source=pwa',
                scope: '/',
                display: 'standalone',
                orientation: 'portrait',
                background_color: '#ffffff',
                theme_color: '#1E3A8A',
                icons: [
                    { src: '/icons/icon-72.png', sizes: '72x72', type: 'image/png' },
                    { src: '/icons/icon-96.png', sizes: '96x96', type: 'image/png' },
                    { src: '/icons/icon-128.png', sizes: '128x128', type: 'image/png' },
                    { src: '/icons/icon-144.png', sizes: '144x144', type: 'image/png' },
                    { src: '/icons/icon-152.png', sizes: '152x152', type: 'image/png' },
                    { src: '/icons/icon-192.png', sizes: '192x192', type: 'image/png' },
                    { src: '/icons/icon-384.png', sizes: '384x384', type: 'image/png' },
                    { src: '/icons/icon-512.png', sizes: '512x512', type: 'image/png' },
                    { src: '/icons/icon-512-maskable.png', sizes: '512x512', type: 'image/png', purpose: 'maskable' },
                ],
            },
            workbox: {
                globPatterns: [
                    'build/**/*.{js,css,html,ico,png,svg,webmanifest}',
                    'icons/**/*.{png,svg}',
                    'offline.html',
                    'apple-touch-icon.png',
                    'favicon.ico',
                    'favicon.svg',
                    'robots.txt',
                ],
                navigateFallback: '/offline.html',
                navigateFallbackDenylist: [/^\/stripe\//, /^\/storage\//, /^\/build\//, /^\/v1\//],
                runtimeCaching: [
                    {
                        urlPattern: ({ request }) => request.mode === 'navigate',
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'pages',
                            networkTimeoutSeconds: 4,
                            expiration: {
                                maxEntries: 30,
                                maxAgeSeconds: 60 * 60 * 24,
                            },
                        },
                    },
                    {
                        urlPattern: ({ url }) => url.pathname.startsWith('/v1/'),
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'api',
                            networkTimeoutSeconds: 4,
                            expiration: {
                                maxEntries: 50,
                                maxAgeSeconds: 60 * 60,
                            },
                        },
                    },
                    {
                        urlPattern: ({ request }) => request.destination === 'image',
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'images',
                            expiration: {
                                maxEntries: 100,
                                maxAgeSeconds: 60 * 60 * 24 * 30,
                            },
                        },
                    },
                ],
            },
            devOptions: {
                enabled: process.env.VITE_PWA_DEV === 'true',
            },
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
