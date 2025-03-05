import type {UserConfig} from "vite";
import path from "path";
import {makeViteConfig} from "../../vite.base";

const widgetConfig: UserConfig = {
    build: {
        rollupOptions: {
            input: {
                widget: path.resolve(__dirname, "../../src/widget/widget.ts"),
                "integrations/ninja-forms": path.resolve(
                    __dirname,
                    "../../src/widget/integrations/ninja-forms.ts",
                ),
                "integrations/woo-blocks-checkout": path.resolve(
                    __dirname,
                    "../../src/widget/integrations/woo-blocks-checkout.ts",
                ),
            }
        },
    },
};

export default makeViteConfig("widget", widgetConfig);
