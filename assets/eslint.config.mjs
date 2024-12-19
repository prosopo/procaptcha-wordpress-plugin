import globals from "globals";
import tsEslint from "typescript-eslint";
import pluginReact from "eslint-plugin-react";
import pluginReactHooks from "eslint-plugin-react-hooks";
import {fixupPluginRules} from "@eslint/compat";
import reactRefresh from "eslint-plugin-react-refresh";

export default [
    {
        files: ["**/*.{js,mjs,cjs,ts,jsx,tsx}"]
    },
    {
        languageOptions: {
            globals: globals.browser
        },
        settings: {
            react: {
                version: "detect",
            },
        },
        // these packages don't support .config.mjs yet, so use the old style.
        plugins: {
            "react-hooks": fixupPluginRules(pluginReactHooks),
            "react-refresh": reactRefresh,
        },
        rules: {
            ...pluginReactHooks.configs.recommended.rules,
        },
    },
    ...tsEslint.configs.recommended,
    pluginReact.configs.flat.recommended,
    pluginReact.configs.flat["jsx-runtime"],
];
