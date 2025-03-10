import { WooBlocksCheckoutIntegrationComponent } from "./wooBlocksCheckoutIntegrationComponent.js";
import type { Integration } from "../../../integration.js";
import type { IntegrationComponent } from "../../../integrationComponent.js";
import type { WebComponentSettings } from "../../../webComponent/webComponentSettings.js";
import type Logger from "../../../../logger/logger.js";

class WoocommerceBlocksCheckoutIntegration implements Integration {
	getIntegrationName(): string {
		return "woocommerce-blocks-checkout";
	}

	createIntegrationComponent(componentLogger: Logger): IntegrationComponent {
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
