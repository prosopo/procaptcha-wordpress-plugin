import type { UserConfig } from "vite";
import path from "path";
import { makeViteConfig } from "../../vite.base.js";

const pathToProcaptchaIntegration = path.resolve(
	__dirname,
	"../../src/procaptchaIntegration/procaptcha-integration.ts",
);

const widgetConfig: UserConfig = {
	build: {
		rollupOptions: {
			input: {
				"procaptcha-integration": pathToProcaptchaIntegration,
				"plugins/ninja-forms/ninja-forms-integration": path.resolve(
					__dirname,
					"../../src/procaptchaIntegration/plugins/ninja-forms/ninja-forms-integration.ts",
				),
				"plugins/woocommerce/woocommerce-integration": path.resolve(
					__dirname,
					"../../src/procaptchaIntegration/plugins/woocommerce/woocommerce-integration.ts",
				),
			},
			output: {
				manualChunks: {
					// make sure the integration bundle is single to avoid any extra http requests.
					"procaptcha-integration": [pathToProcaptchaIntegration],
				},
			},
		},
	},
};

export default makeViteConfig("procaptcha-integration", widgetConfig);
