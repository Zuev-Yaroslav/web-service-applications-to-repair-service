import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig, loadEnv } from 'vite';
import * as path from 'node:path';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd());

    return {
        resolve: {
            alias: {
                'ziggy-js': path.resolve('vendor/tightenco/ziggy'),
            },
        },
        plugins: [
            laravel({
                input: ['resources/js/app.ts'],
                ssr: 'resources/js/ssr.ts',
                refresh: true,
            }),
            tailwindcss(),
            wayfinder({
                formVariants: true,
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
        server: {
            https: false,
            host: true,
            port: env.VITE_PORT,
            hmr: {host: 'localhost', protocol: 'ws'},
        },
    }
});
