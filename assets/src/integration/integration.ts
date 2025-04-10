import type Logger from "#logger/logger.js";
import type { WebComponent } from "#webComponent/webComponent.js";
import type { WebComponentSettings } from "#webComponent/webComponentSettings.js";

interface Integration {
	getIntegrationName(): string;

	createIntegrationComponent(componentLogger: Logger): WebComponent;

	getIntegrationWebComponentSettings(): WebComponentSettings;
}

export { Integration };
