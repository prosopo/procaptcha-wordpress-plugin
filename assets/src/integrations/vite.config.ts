import type { UserConfig } from "vite";
import path from "path";
import { makeViteConfig } from "../../vite.base.js";
import integrations from "./integrations.json" assert { type: "json" };

const integrationFiles = Object.fromEntries(
	integrations.map((integration) => [
		`${integration}`,
		path.resolve(__dirname, `./${integration}.ts`),
	]),
);

const viteSettings: UserConfig = {
	build: {
		rollupOptions: {
			input: integrationFiles,
			output: {
				manualChunks: {
					// make sure the integration bundle is single to avoid any extra http requests.
					"procaptcha/procaptcha-integration": [
						integrationFiles["procaptcha/procaptcha-integration"],
					],
				},
			},
		},
	},
};

export default makeViteConfig("integrations", viteSettings);
