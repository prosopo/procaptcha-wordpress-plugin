import { createIntegrationConnector } from "../../../connector/integrationConnectorFactory.js";
import { WoocommerceBlocksCheckoutIntegration } from "./woocommerceBlocksCheckoutIntegration.js";

const woocommerceBlocksCheckoutIntegration =
	new WoocommerceBlocksCheckoutIntegration();

const integrationConnector = createIntegrationConnector();
integrationConnector.connectIntegration(woocommerceBlocksCheckoutIntegration);
