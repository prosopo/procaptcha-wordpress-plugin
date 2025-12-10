import { createIntegrationConnector } from "#integration/connector/integrationConnectorFactory.js";
import type { Integration } from "#integration/integration.js";
import type Logger from "../../utils/logger/logger.js";
import FormIntegrationComponent from "#integrations/procaptcha/formIntegrationComponent.js";
import WidgetIntegrationComponent from "#integrations/procaptcha/widgetIntegrationComponent.js";

const integrationConnector = createIntegrationConnector();

const formIntegration: Integration = {
	name: "form",
	createWebComponent: (componentLogger: Logger) => {
		return new FormIntegrationComponent(componentLogger);
	},
	getWebComponentSettings: () => {
		return {
			name: "prosopo-procaptcha-wp-form",
			processIfReconnected: false,
			waitWindowLoadedInsteadOfDomLoaded: false,
		};
	},
};

const widgetIntegration: Integration = {
	name: "widget",
	createWebComponent: (componentLogger: Logger) => {
		return new WidgetIntegrationComponent(componentLogger);
	},
	getWebComponentSettings: () => {
		return {
			name: "prosopo-procaptcha-wp-widget",
			processIfReconnected: false,
			// wait, case we need to make sure window.procaptcha is available.
			waitWindowLoadedInsteadOfDomLoaded: true,
		};
	},
};

integrationConnector.connectIntegration(formIntegration);
integrationConnector.connectIntegration(widgetIntegration);
