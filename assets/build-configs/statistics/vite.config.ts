import type {UserConfig} from 'vite';
import path from 'path';
import react from '@vitejs/plugin-react';
import {visualizer} from "rollup-plugin-visualizer";
import {makeViteConfig} from "../vite.config";

const statisticsConfig: UserConfig = {
    plugins: [
        react(),
        visualizer(),
    ],
    build: {
        rollupOptions: {
            input: {
                'statistics': path.resolve(__dirname, '../../src/statistics.tsx'),
                'statistics-styles': path.resolve(__dirname, '../../src/statistics.scss'),
            }
        },
    },
    css: {
        postcss: path.resolve(__dirname, './postcss.config.js'),
    }
};

export default makeViteConfig("statistics", statisticsConfig);
