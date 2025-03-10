import type { IntegrationComponent } from "../../integration/integrationComponent.js";
import WidgetIntegrationComponent from "./widgetIntegrationComponent.js";
import type { Integration } from "../../integration/integration.js";
import type Logger from "../../logger/logger.js";
import type { WebComponentSettings } from "../../integration/webComponent/webComponentSettings.js";

class WidgetIntegration implements Integration {
	getIntegrationName(): string {
		return "widget";
	}

	public createIntegrationComponent(
		componentLogger: Logger,
	): IntegrationComponent {
		return new WidgetIntegrationComponent(componentLogger);
	}

	public getIntegrationWebComponentSettings(): WebComponentSettings {
		return {
			name: "prosopo-procaptcha-wp-widget",
			processIfReconnected: false,
			// wait, case we need to make sure window.procaptcha is available.
			waitWindowLoadedInsteadOfDomLoaded: true,
		};
	}
}

export { WidgetIntegration };
