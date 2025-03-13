import { StatisticsWebComponent } from "./components/statisticsWebComponent.js";
import { WebComponentRegistrar } from "../../webComponent/webComponentRegistrar.js";
import LoggerFactory from "../../logger/loggerFactory.js";
import PluginModuleLogger from "../../logger/plugin/pluginModuleLogger.js";
import type Logger from "../../logger/logger.js";

class Statistics {
	private readonly logger: Logger;
	private readonly webComponentRegistrar: WebComponentRegistrar;

	public constructor() {
		const loggerFactory = new LoggerFactory();
		const pluginModuleLogger = new PluginModuleLogger();

		this.logger = loggerFactory.createLogger(
			"statistics",
			pluginModuleLogger,
		);
		this.webComponentRegistrar = new WebComponentRegistrar(this.logger);
	}

	public setupWebComponent(): void {
		const statisticsWebComponent = new StatisticsWebComponent(this.logger);

		this.webComponentRegistrar.registerWebComponent(
			statisticsWebComponent,
			{
				name: "procaptcha-statistics",
				processIfReconnected: false,
				waitWindowLoadedInsteadOfDomLoaded: false,
			},
		);
	}
}

new Statistics().setupWebComponent();
