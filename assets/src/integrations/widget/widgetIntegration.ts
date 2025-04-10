import type { WebComponent } from "#webComponent/webComponent.js";
import WidgetIntegrationComponent from "./widgetIntegrationComponent.js";
import type { Integration } from "#integration/integration.js";
import type { WebComponentSettings } from "#webComponent/webComponentSettings.js";
import type Logger from "#logger/logger.js";

class WidgetIntegration implements Integration {
	getIntegrationName(): string {
		return "widget";
	}

	public createIntegrationComponent(componentLogger: Logger): WebComponent {
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
