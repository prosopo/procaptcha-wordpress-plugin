import type {UserConfig} from 'vite';
import path from 'path';
import {makeViteConfig} from "../vite.config";

const settingsConfig: UserConfig = {
    build: {
        rollupOptions: {
            input: {
                settings: path.resolve(__dirname, '../../src/settings.scss'),
            },
        },
    },
    css: {
        postcss: path.resolve(__dirname, './postcss.config.js'),
    },
};

export default makeViteConfig("settings", settingsConfig);
