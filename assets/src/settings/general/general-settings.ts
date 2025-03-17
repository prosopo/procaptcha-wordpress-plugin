import LoggerFactory from "../../logger/loggerFactory.js";
import PluginModuleLogger from "../../logger/plugin/pluginModuleLogger.js";
import type Logger from "../../logger/logger.js";
import { WebComponentRegistrar } from "../../webComponent/webComponentRegistrar.js";
import { GeneralSettingsWebComponent } from "./generalSettingsWebComponent.js";
import { GeneralSettingsConfig } from "./generalSettingsConfig.js";

class GeneralSettings {
	private readonly logger: Logger;
	private readonly webComponentRegistrar: WebComponentRegistrar;
	private readonly config: GeneralSettingsConfig;

	public constructor() {
		const loggerFactory = new LoggerFactory();
		const pluginModuleLogger = new PluginModuleLogger();

		this.logger = loggerFactory.createLogger(
			"statistics",
			pluginModuleLogger,
		);
		this.webComponentRegistrar = new WebComponentRegistrar(this.logger);

		this.config = new GeneralSettingsConfig();
	}

	public setupWebComponent(): void {
		const generalSettingsWebComponent = new GeneralSettingsWebComponent(
			this.config.getAccountApiEndpoint(),
			this.config.getSiteKey(),
			this.config.getSecretKey(),
			this.logger,
		);

		this.webComponentRegistrar.registerWebComponent(
			generalSettingsWebComponent,
			{
				name: "general-procaptcha-settings",
				processIfReconnected: false,
				waitWindowLoadedInsteadOfDomLoaded: false,
			},
		);
	}
}

new GeneralSettings().setupWebComponent();
