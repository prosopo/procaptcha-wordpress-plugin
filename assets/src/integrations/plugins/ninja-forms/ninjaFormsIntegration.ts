import { NinjaFormsIntegrationComponent } from "./ninjaFormsIntegrationComponent.js";
import type { Integration } from "../../../integration/integration.js";
import type { WebComponent } from "../../../webComponent/webComponent.js";
import type { WebComponentSettings } from "../../../webComponent/webComponentSettings.js";
import type Logger from "../../../logger/logger.js";

class NinjaFormsIntegration implements Integration {
	getIntegrationName(): string {
		return "ninja-forms";
	}

	createIntegrationComponent(componentLogger: Logger): WebComponent {
		return new NinjaFormsIntegrationComponent(componentLogger);
	}

	getIntegrationWebComponentSettings(): WebComponentSettings {
		return {
			name: "prosopo-procaptcha-ninja-forms-integration",
			processIfReconnected: false,
			waitWindowLoadedInsteadOfDomLoaded: true,
		};
	}
}

export { NinjaFormsIntegration };
