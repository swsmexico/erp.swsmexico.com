import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import { VitePWA } from 'vite-plugin-pwa'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        VitePWA({
            registerType: 'autoUpdate',
            includeAssets: ['favicon.ico', 'apple-touch-icon.png'],
            manifest: {
                name: 'SWS Mexico ERP',
                short_name: 'SWS ERP',
                description: 'Sistema de gestión empresarial SWS Mexico',
                theme_color: '#5CC8F2',
                background_color: '#F2F2F2',
                display: 'standalone',
                orientation: 'portrait',
                start_url: '/',
                icons: [
                    { src: 'icons/pwa-192x192.png', sizes: '192x192', type: 'image/png' },
                    { src: 'icons/pwa-512x512.png', sizes: '512x512', type: 'image/png' },
                    { src: 'icons/pwa-512x512.png', sizes: '512x512', type: 'image/png', purpose: 'any maskable' },
                ],
            },
            workbox: {
                globPatterns: ['**/*.{js,css,html,ico,png,svg,woff2}'],
                runtimeCaching: [
                    {
                        urlPattern: /^https:\/\/erp\.swsmexico\.com\/api\/.*/i,
                        handler: 'NetworkFirst',
                        options: { cacheName: 'api-cache', networkTimeoutSeconds: 10 },
                    },
                ],
            },
        }),
    ],
    resolve: {
        alias: { '@': '/resources/js' },
    },
})
