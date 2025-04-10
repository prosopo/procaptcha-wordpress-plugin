import { createIntegrationConnector } from "#integration/connector/integrationConnectorFactory.js";
import { WoocommerceBlocksCheckoutIntegration } from "#integrations/plugins/woocommerce/woocommerceBlocksCheckoutIntegration.js";

const woocommerceBlocksCheckoutIntegration =
	new WoocommerceBlocksCheckoutIntegration();

const integrationConnector = createIntegrationConnector();
integrationConnector.connectIntegration(woocommerceBlocksCheckoutIntegration);
