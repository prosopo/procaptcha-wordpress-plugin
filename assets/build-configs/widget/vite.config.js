import { defineConfig } from "vite";
import path from "path";

export default defineConfig({
	root: ".",
	plugins: [],
	build: {
		outDir: path.resolve(__dirname, "../../../prosopo-procaptcha/dist"),
		rollupOptions: {
			input: {
				widget: path.resolve(__dirname, "../../src/widget/widget.ts"),
			},
			output: {
				entryFileNames: "[name].min.js",
				assetFileNames: "[name].min[extname]",
			},
		},
	},
});
