import {defineConfig} from 'vite';
import path from 'path';
import react from '@vitejs/plugin-react';
import {visualizer} from "rollup-plugin-visualizer";

export default defineConfig({
    root: '.',
    plugins: [
        react(),
        visualizer(),
    ],
    build: {
        outDir: path.resolve(__dirname, '../../../prosopo-procaptcha/dist'),
        rollupOptions: {
            input: {
                script: path.resolve(__dirname, '../../src/statistics.tsx'),
                style: path.resolve(__dirname, '../../src/statistics.scss'),
            },
            output: {
                entryFileNames: 'statistics.min.js',
                assetFileNames: 'statistics.min[extname]',
            }
        },
    },
    css: {
        postcss: path.resolve(__dirname, './postcss.config.js'),
    },
});
