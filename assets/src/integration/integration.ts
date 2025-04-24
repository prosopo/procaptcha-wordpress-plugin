import type Logger from "#logger/logger.js";
import type { WebComponent } from "#webComponent/webComponent.js";
import type { WebComponentSettings } from "#webComponent/webComponentSettings.js";

interface Integration {
	name: string;

	createWebComponent(componentLogger: Logger): WebComponent;

	getWebComponentSettings(): WebComponentSettings;
}

export { Integration };
