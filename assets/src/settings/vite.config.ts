import type { UserConfig } from "vite";
import path from "path";
import { makeViteConfig } from "../../vite.base.js";
import react from "@vitejs/plugin-react";
import { visualizer } from "rollup-plugin-visualizer";
import tailwindcss from "@tailwindcss/vite";
import { nodePolyfills } from "vite-plugin-node-polyfills";

const settingsConfig: UserConfig = {
	plugins: [
		react({
			include: "**/*.tsx",
		}),
		visualizer(),
		tailwindcss(),
		nodePolyfills()
	],
	build: {
		rollupOptions: {
			input: {
				"general/general-settings": path.resolve(
					__dirname,
					"./general/general-settings.ts",
				),
				"general/general-settings-styles": path.resolve(
					__dirname,
					"./general/general-settings-styles.css",
				),
				"statistics/statistics": path.resolve(
					__dirname,
					"./statistics/statistics.ts",
				),
				"statistics/statistics-styles": path.resolve(
					__dirname,
					"./statistics/statistics-styles.css",
				),
			},
		},
	},
};

export default makeViteConfig("settings", settingsConfig);
