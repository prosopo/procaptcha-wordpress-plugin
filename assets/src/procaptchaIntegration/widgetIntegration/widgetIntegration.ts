import LoggerFactory from "../../logger/loggerFactory.js";
import { WebComponentRegistrar } from "../webComponent/webComponentRegistrar.js";
import type ModuleLogger from "../../logger/moduleLogger.js";
import type { WebComponent } from "../webComponent/webComponent.js";
import WidgetIntegrationComponent from "./widgetIntegrationComponent.js";

class WidgetIntegration {
	public constructor(
		private readonly loggerFactory: LoggerFactory,
		private readonly moduleLogger: ModuleLogger,
		private readonly webComponentRegistrar: WebComponentRegistrar,
	) {}

	public createWidgetIntegrationComponent(): WebComponent {
		const rendererLogger = this.loggerFactory.makeLogger(
			"widget-renderer",
			this.moduleLogger,
		);

		return new WidgetIntegrationComponent(rendererLogger);
	}

	public registerWidgetIntegrationComponent(
		widgetIntegration: WebComponent,
	): void {
		this.webComponentRegistrar.registerWebComponent({
			name: "prosopo-procaptcha-wp-widget",
			componentClass: widgetIntegration,
			processIfReconnected: false,
			// wait, case we need to make sure window.procaptcha is available.
			waitWindowLoadedInsteadOfDomLoaded: true,
		});
	}
}

export { WidgetIntegration };
