import { WebComponentRegistrar } from "../../webComponent/webComponentRegistrar.js";
import LoggerFactory from "../../../logger/loggerFactory.js";
import PluginModuleLogger from "../../../logger/plugin/pluginModuleLogger.js";
import { WooBlocksCheckoutComponent } from "./wooBlocksCheckoutComponent.js";
import { NinjaFormsComponent } from "../ninja-forms/ninjaFormsComponent.js";

class WoocommerceIntegration {
	private readonly componentRegistrar: WebComponentRegistrar;
	private readonly wooBlocksCheckoutComponent: WooBlocksCheckoutComponent;

	public constructor() {
		const loggerFactory = new LoggerFactory();
		const moduleLogger = new PluginModuleLogger();

		this.wooBlocksCheckoutComponent = new WooBlocksCheckoutComponent(
			loggerFactory.makeLogger("woo-blocks-checkout", moduleLogger),
		);

		const componentLogger = loggerFactory.makeLogger(
			"web-component-registar",
			moduleLogger,
		);

		this.componentRegistrar = new WebComponentRegistrar(componentLogger);
	}

	public setupIntegration(): void {
		this.componentRegistrar.registerWebComponent({
			name: "prosopo-procaptcha-woo-checkout-form",
			componentClass: this.wooBlocksCheckoutComponent,
			processIfReconnected: false,
			waitWindowLoadedInsteadOfDomLoaded: true,
		});
	}
}

new WoocommerceIntegration().setupIntegration();
