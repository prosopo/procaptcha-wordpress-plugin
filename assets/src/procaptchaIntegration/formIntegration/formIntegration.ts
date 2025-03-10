import type { WebComponent } from "../webComponent/webComponent.js";
import FormIntegrationComponent from "./formIntegrationComponent.js";
import LoggerFactory from "../../logger/loggerFactory.js";
import type ModuleLogger from "../../logger/moduleLogger.js";
import { WebComponentRegistrar } from "../webComponent/webComponentRegistrar.js";

class FormIntegration {
	public constructor(
		private readonly loggerFactory: LoggerFactory,
		private readonly moduleLogger: ModuleLogger,
		private readonly webComponentRegistrar: WebComponentRegistrar,
	) {}

	public createFormIntegrationComponent(): WebComponent {
		const formValidatorLogger = this.loggerFactory.makeLogger(
			"form-validator",
			this.moduleLogger,
		);

		return new FormIntegrationComponent(formValidatorLogger);
	}

	public registerFormIntegrationComponent(
		formIntegration: WebComponent,
	): void {
		this.webComponentRegistrar.registerWebComponent({
			name: "prosopo-procaptcha-wp-form",
			componentClass: formIntegration,
			processIfReconnected: false,
			waitWindowLoadedInsteadOfDomLoaded: false,
		});
	}
}

export { FormIntegration };
