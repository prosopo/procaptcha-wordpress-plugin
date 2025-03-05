import {UserConfig} from "vite";
import path from "path";
import deepmerge from "deepmerge";

const defaultConfig: UserConfig = {
    root: ".",
    build: {
        outDir: path.resolve(__dirname, '../prosopo-procaptcha/dist'),
        emptyOutDir: true,
        rollupOptions: {
            output: {
                entryFileNames: "[name].min.js",
                assetFileNames: "[name].min[extname]",
            },
        },
    },
    define: {
        // workaround for @prosopo/contract, which uses 'process.env' instead of 'import.meta.env'.
        'process.env': {}
    }
};

function makeViteConfig(outputSubdirectoryName: string, customSettings: UserConfig): UserConfig {
    const config = deepmerge(defaultConfig, customSettings);

    config.build.outDir = path.resolve(config.build.outDir, outputSubdirectoryName);

    return config;
}


export {makeViteConfig};
