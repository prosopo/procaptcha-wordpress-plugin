import { createIntegrationConnector } from "#integration/connector/integrationConnectorFactory.js";
import type Logger from "../../../utils/logger/logger.js";
import { WooBlocksCheckoutIntegrationComponent } from "#integrations/plugins/woocommerce/wooBlocksCheckoutIntegrationComponent.js";
import type { Integration } from "#integration/integration.js";

const integrationConnector = createIntegrationConnector();

const wooBlocksCheckoutIntegration: Integration = {
	name: "woocommerce-blocks-checkout",

	createWebComponent: (componentLogger: Logger) =>
		new WooBlocksCheckoutIntegrationComponent(componentLogger),

	getWebComponentSettings: () => ({
		name: "prosopo-procaptcha-woo-checkout-form",
		processIfReconnected: false,
		waitWindowLoadedInsteadOfDomLoaded: true,
	}),
};

integrationConnector.connectIntegration(wooBlocksCheckoutIntegration);
