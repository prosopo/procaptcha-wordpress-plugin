import { createIntegrationConnector } from "#integration/connector/integrationConnectorFactory.js";
import type { Integration } from "#integration/integration.js";
import type Logger from "#logger/logger.js";
import type { WebComponent } from "#webComponent/webComponent.js";
import { NinjaFormsIntegrationComponent } from "#integrations/plugins/ninja-forms/ninjaFormsIntegrationComponent.js";
import type { WebComponentSettings } from "#webComponent/webComponentSettings.js";

const integrationConnector = createIntegrationConnector();

const ninjaFormsIntegration: Integration = {
	name: "ninja-forms",

	createWebComponent: (componentLogger: Logger): WebComponent =>
		new NinjaFormsIntegrationComponent(componentLogger),

	getWebComponentSettings: (): WebComponentSettings => ({
		name: "prosopo-procaptcha-ninja-forms-integration",
		processIfReconnected: false,
		waitWindowLoadedInsteadOfDomLoaded: true,
	}),
};

integrationConnector.connectIntegration(ninjaFormsIntegration);
