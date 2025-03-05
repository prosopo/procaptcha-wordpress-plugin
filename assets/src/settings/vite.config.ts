import type {UserConfig} from 'vite';
import path from 'path';
import {makeViteConfig} from "../../vite.base";
import react from "@vitejs/plugin-react";
import {visualizer} from "rollup-plugin-visualizer";

const settingsConfig: UserConfig = {
    plugins: [
        react(),
        visualizer(),
    ],
    build: {
        rollupOptions: {
            input: {
                settings: path.resolve(__dirname, './settings.scss'),
                'statistics': path.resolve(__dirname, './statistics.tsx'),
                'statistics-styles': path.resolve(__dirname, './statistics-styles.scss'),
            },
        },
    },
    css: {
        postcss: path.resolve(__dirname, './postcss.config.js'),
    },
};

export default makeViteConfig("settings", settingsConfig);
