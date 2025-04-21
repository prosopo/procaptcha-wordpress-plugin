import type { UserConfig } from "vite";
import path from "path";
import { makeViteConfig } from "../../vite.base.js";
import pluginIntegrations from "./plugins/pluginIntegrations.json";

const procaptchaIntegrationFile = path.resolve(
	__dirname,
	"./procaptcha-integration.ts",
);

const pluginIntegrationFiles = Object.fromEntries(
	pluginIntegrations.map((pluginIntegration) => [
		`plugins/${pluginIntegration}`,
		path.resolve(__dirname, `./plugins/${pluginIntegration}.ts`),
	]),
);

const widgetConfig: UserConfig = {
	build: {
		rollupOptions: {
			input: {
				"procaptcha-integration": procaptchaIntegrationFile,
				...pluginIntegrationFiles,
			},
			output: {
				manualChunks: {
					// make sure the integration bundle is single to avoid any extra http requests.
					"procaptcha-integration": [procaptchaIntegrationFile],
				},
			},
		},
	},
};

export default makeViteConfig("integrations", widgetConfig);
