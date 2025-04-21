import type { WebComponent } from "#webComponent/webComponent.js";
import FormIntegrationComponent from "#integrations/form/formIntegrationComponent.js";
import type { Integration } from "#integration/integration.js";
import type { WebComponentSettings } from "#webComponent/webComponentSettings.js";
import type Logger from "#logger/logger.js";

class FormIntegration implements Integration {
	getName(): string {
		return "form";
	}

	public createWebComponent(componentLogger: Logger): WebComponent {
		return new FormIntegrationComponent(componentLogger);
	}

	public getWebComponentSettings(): WebComponentSettings {
		return {
			name: "prosopo-procaptcha-wp-form",
			processIfReconnected: false,
			waitWindowLoadedInsteadOfDomLoaded: false,
		};
	}
}

export { FormIntegration };
