import type Logger from "../utils/logger/logger.js";
import type { WebComponent } from "../utils/webComponent/webComponent.js";
import type { WebComponentSettings } from "../utils/webComponent/webComponentSettings.js";

interface Integration {
	name: string;

	createWebComponent(componentLogger: Logger): WebComponent;

	getWebComponentSettings(): WebComponentSettings;
}

export { Integration };
