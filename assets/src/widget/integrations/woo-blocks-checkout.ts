import ComponentControllerInterface from "../../interfaces/componentControllerInterface.js";
import Logger from "../../logger/logger.js";
import { WebComponentRegistrar } from "../webComponent/webComponentRegistrar.js";
import LoggerFactory from "../../logger/loggerFactory.js";
import PluginModuleLogger from "../../logger/plugin/pluginModuleLogger.js";

class WooBlocksCheckoutIntegration implements ComponentControllerInterface {
	private readonly logger: Logger;

	constructor(logger: Logger) {
		this.logger = logger;
	}

	processElement(origin: HTMLElement): void {
		const form = origin.closest("form");

		// add a stub to bypass Woo client validation, and run server,
		// otherwise it's confusing as the input is hidden.
		this.updateInputValue("default");

		form.addEventListener(
			"_prosopo-procaptcha__filled",
			(event: CustomEvent) => {
				this.updateInputValue(event.detail.token);
			},
		);
	}

	protected updateInputValue(token: string): void {
		if (
			false === window.hasOwnProperty("wp") ||
			false === window["wp"].hasOwnProperty("data")
		) {
			this.logger.warning("window.wp.data is not available");
			return;
		}

		window["wp"].data.dispatch("wc/store/checkout").setAdditionalFields({
			"prosopo-procaptcha/prosopo_procaptcha": token,
		});
	}
}

const loggerFactory = new LoggerFactory();
const moduleLogger = new PluginModuleLogger();

const wooBlocksCheckout = new WooBlocksCheckoutIntegration(
	loggerFactory.makeLogger("woo-blocks-checkout", moduleLogger),
);

const componentLogger = loggerFactory.makeLogger(
	"web-component-registar",
	moduleLogger,
);

const webComponentRegistrar = new WebComponentRegistrar(componentLogger);

webComponentRegistrar.registerWebComponent({
	name: "prosopo-procaptcha-woo-checkout-form",
	componentController: wooBlocksCheckout,
	processIfReconnected: false,
	waitWindowLoadedInsteadOfDomLoaded: true,
});
