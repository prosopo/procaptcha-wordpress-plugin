import type { Integration } from "#integration/integration.js";
import type { WebComponent } from "#webComponent/webComponent.js";
import type { WebComponentSettings } from "#webComponent/webComponentSettings.js";
import type Logger from "#logger/logger.js";
import { WooBlocksCheckoutIntegrationComponent } from "./wooBlocksCheckoutIntegrationComponent.js";

class WoocommerceBlocksCheckoutIntegration implements Integration {
	getIntegrationName(): string {
		return "woocommerce-blocks-checkout";
	}

	createIntegrationComponent(componentLogger: Logger): WebComponent {
		return new WooBlocksCheckoutIntegrationComponent(componentLogger);
	}

	getIntegrationWebComponentSettings(): WebComponentSettings {
		return {
			name: "prosopo-procaptcha-woo-checkout-form",
			processIfReconnected: false,
			waitWindowLoadedInsteadOfDomLoaded: true,
		};
	}
}

export { WoocommerceBlocksCheckoutIntegration };
