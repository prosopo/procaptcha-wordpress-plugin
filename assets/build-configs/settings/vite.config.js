import {defineConfig} from 'vite';
import path from 'path';

export default defineConfig({
    root: '.',
    build: {
        outDir: path.resolve(__dirname, '../../../prosopo-procaptcha/dist'),
        rollupOptions: {
            input: {
                settings: path.resolve(__dirname, '../../src/settings.scss'),
            },
            output: {
                assetFileNames: '[name].min[extname]',
            }
        },
    },
    css: {
        postcss: path.resolve(__dirname, './postcss.config.js'),
    },
});
