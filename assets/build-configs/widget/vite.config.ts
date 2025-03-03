import type {UserConfig} from "vite";
import path from "path";
import {makeViteConfig} from "../vite.config";

const widgetConfig: UserConfig = {
    build: {
        rollupOptions: {
            input: {
                widget: path.resolve(__dirname, "../../src/widget/widget.ts"),
            },
        },
    },
};

export default makeViteConfig("widget", widgetConfig);
