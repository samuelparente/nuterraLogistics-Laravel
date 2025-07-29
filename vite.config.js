import { defineConfig, loadEnv } from 'vite'; // <- defineConfig está aqui!
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd());

    return {
        base: '/build/', // Ajusta o caminho base como combinámos!
        build: {
            outDir: 'public/build',
            assetsDir: 'assets',
        },
        plugins: [
            laravel({
                input: [
                    'resources/css/icons.css',
                    'resources/js/app.js',
                    'resources/js/dashboard.js',
                ],
                refresh: true,
            }),
        ],
    };
});
