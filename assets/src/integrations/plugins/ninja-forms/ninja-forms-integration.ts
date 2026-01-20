import { createIntegrationConnector } from "#integration/connector/integrationConnectorFactory.js";
import type { Integration } from "#integration/integration.js";
import type Logger from "../../../utils/logger/logger.js";
import type { WebComponent } from "../../../utils/webComponent/webComponent.js";
import { NinjaFormsIntegrationComponent } from "#integrations/plugins/ninja-forms/ninjaFormsIntegrationComponent.js";
import type { WebComponentSettings } from "../../../utils/webComponent/webComponentSettings.js";

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
