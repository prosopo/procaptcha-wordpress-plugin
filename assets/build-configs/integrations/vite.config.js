import { defineConfig } from "vite";
import path from "path";

export default defineConfig({
	root: ".",
	plugins: [],
	build: {
		outDir: path.resolve(
			__dirname,
			"../../../prosopo-procaptcha/dist/integrations",
		),
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
			output: {
				entryFileNames: "[name].min.js",
				assetFileNames: "[name].min[extname]",
			},
		},
	},
});
