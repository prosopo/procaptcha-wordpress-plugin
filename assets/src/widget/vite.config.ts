import type { UserConfig } from "vite";
import path from "path";
import { makeViteConfig } from "../../vite.base";

const pathToWidget = path.resolve(__dirname, "../../src/widget/widget.ts");

const widgetConfig: UserConfig = {
	build: {
		rollupOptions: {
			input: {
				widget: pathToWidget,
				"integrations/ninja-forms": path.resolve(
					__dirname,
					"../../src/widget/integrations/ninja-forms.ts",
				),
				"integrations/woo-blocks-checkout": path.resolve(
					__dirname,
					"../../src/widget/integrations/woo-blocks-checkout.ts",
				),
			},
			output: {
				manualChunks: {
					// make sure the widget bundle is single to avoid any extra http requests.
					widget: [pathToWidget],
				},
			},
		},
	},
};

export default makeViteConfig("widget", widgetConfig);
