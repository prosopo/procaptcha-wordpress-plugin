import { WebComponentRegistrar } from "#webComponent/webComponentRegistrar.js";
import { GeneralSettingsWebComponent } from "./generalSettingsWebComponent.js";
import { GeneralSettingsConfig } from "./generalSettingsConfig.js";
import type Logger from "#logger/logger.js";
import type { AccountApiResolver } from "#settings/account/api/accountApiResolver.js";
import type { ApiCredentials } from "#settings/apiCredentials.js";
import LoggerFactory from "#logger/loggerFactory.js";
import PluginModuleLogger from "#logger/plugin/pluginModuleLogger.js";
import { ProsopoAccountApi } from "#settings/account/api/prosopoAccountApi.js";
import { AccountApiCredentials } from "#settings/account/api/accountApiCredentials.js";

class GeneralSettings {
	private readonly logger: Logger;
	private readonly webComponentRegistrar: WebComponentRegistrar;
	private readonly config: GeneralSettingsConfig;
	private readonly accountApiResolver: AccountApiResolver;
	private readonly accountApiCredentials: ApiCredentials;

	public constructor() {
		const loggerFactory = new LoggerFactory();
		const pluginModuleLogger = new PluginModuleLogger();

		this.logger = loggerFactory.createLogger(
			"statistics",
			pluginModuleLogger,
		);
		this.webComponentRegistrar = new WebComponentRegistrar(this.logger);

		this.config = new GeneralSettingsConfig();

		this.accountApiResolver = new ProsopoAccountApi(
			this.config.getAccountApiEndpoint(),
			this.logger,
		);

		this.accountApiCredentials = new AccountApiCredentials(
			this.config.getSiteKey(),
			this.config.getSecretKey(),
		);
	}

	public setupWebComponent(): void {
		const generalSettingsWebComponent = new GeneralSettingsWebComponent(
			this.accountApiCredentials,
			this.accountApiResolver,
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
