import { createIntegrationConnector } from "#integration/connector/integrationConnectorFactory.js";
import type { Integration } from "#integration/integration.js";
import type Logger from "../../../utils/logger/logger.js";
import type { WebComponent } from "../../../utils/webComponent/webComponent.js";
import type { WebComponentSettings } from "../../../utils/webComponent/webComponentSettings.js";
import { BeaverBuilderIntegrationComponent } from "#integrations/plugins/beaver-builder/beaverBuilderIntegrationComponent.js";

const integrationConnector = createIntegrationConnector();

const beaverBuilderIntegration: Integration = {
	name: "beaver-builder",
	createWebComponent: (componentLogger: Logger): WebComponent =>
		new BeaverBuilderIntegrationComponent(componentLogger),
	getWebComponentSettings: (): WebComponentSettings => ({
		name: "prosopo-procaptcha-beaver-builder-integration",
		processIfReconnected: false,
		waitWindowLoadedInsteadOfDomLoaded: true,
	}),
};

integrationConnector.connectIntegration(beaverBuilderIntegration);
