import type { IntegrationComponent } from "../../integration/integrationComponent.js";
import FormIntegrationComponent from "./formIntegrationComponent.js";
import type { Integration } from "../../integration/integration.js";
import type { WebComponentSettings } from "../../integration/webComponent/webComponentSettings.js";
import type Logger from "../../logger/logger.js";

class FormIntegration implements Integration {
	getIntegrationName(): string {
		return "form";
	}

	public createIntegrationComponent(
		componentLogger: Logger,
	): IntegrationComponent {
		return new FormIntegrationComponent(componentLogger);
	}

	public getIntegrationWebComponentSettings(): WebComponentSettings {
		return {
			name: "prosopo-procaptcha-wp-form",
			processIfReconnected: false,
			waitWindowLoadedInsteadOfDomLoaded: false,
		};
	}
}

export { FormIntegration };
