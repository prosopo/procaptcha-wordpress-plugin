import { WebComponentRegistrar } from "../../webComponent/webComponentRegistrar.js";
import LoggerFactory from "../../../logger/loggerFactory.js";
import PluginModuleLogger from "../../../logger/plugin/pluginModuleLogger.js";
import { NinjaFormsComponent } from "./ninjaFormsComponent.js";

class NinjaFormsIntegration {
	private readonly componentRegistrar: WebComponentRegistrar;
	private readonly ninjaFormsComponent: NinjaFormsComponent;

	public constructor() {
		const loggerFactory = new LoggerFactory();
		const moduleLogger = new PluginModuleLogger();

		this.ninjaFormsComponent = new NinjaFormsComponent(
			loggerFactory.makeLogger("ninja-forms", moduleLogger),
		);

		const componentLogger = loggerFactory.makeLogger(
			"web-component-registrar",
			moduleLogger,
		);

		this.componentRegistrar = new WebComponentRegistrar(componentLogger);
	}

	public setupIntegration(): void {
		this.componentRegistrar.registerWebComponent({
			name: "prosopo-procaptcha-ninja-forms-integration",
			componentClass: this.ninjaFormsComponent,
			processIfReconnected: false,
			waitWindowLoadedInsteadOfDomLoaded: true,
		});
	}
}

new NinjaFormsIntegration().setupIntegration();
