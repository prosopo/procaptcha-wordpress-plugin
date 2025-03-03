import type {UserConfig} from "vite";
import path from "path";
import {makeViteConfig} from "../vite.config";

const integrationsConfig: UserConfig = {
    build: {
        rollupOptions: {
            input: {
                "ninja-forms": path.resolve(
                    __dirname,
                    "../../src/integrations/ninja-forms.ts",
                ),
                "woo-blocks-checkout": path.resolve(
                    __dirname,
                    "../../src/integrations/woo-blocks-checkout.ts",
                ),
            },
        },
    },
};

export default makeViteConfig("integrations", integrationsConfig);
