import type { WebComponent } from "../../webComponent/webComponent.js";
import FormIntegrationComponent from "./formIntegrationComponent.js";
import type { Integration } from "../../integration/integration.js";
import type { WebComponentSettings } from "../../webComponent/webComponentSettings.js";
import type Logger from "../../logger/logger.js";

class FormIntegration implements Integration {
	getIntegrationName(): string {
		return "form";
	}

	public createIntegrationComponent(componentLogger: Logger): WebComponent {
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
