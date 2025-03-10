import type { IntegrationComponent } from "./integrationComponent.js";
import type { WebComponentSettings } from "./webComponent/webComponentSettings.js";
import type Logger from "../logger/logger.js";

interface Integration {
	getIntegrationName(): string;

	createIntegrationComponent(componentLogger: Logger): IntegrationComponent;

	getIntegrationWebComponentSettings(): WebComponentSettings;
}

export { Integration };
