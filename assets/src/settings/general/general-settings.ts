import { WebComponentRegistrar } from "#utils/webComponent/webComponentRegistrar.js";
import { GeneralSettingsWebComponent } from "./generalSettingsWebComponent.js";
import { GeneralSettingsConfig } from "./generalSettingsConfig.js";
import type Logger from "#utils/logger/logger.js";
import {
	type ApiCredentials,
	SiteApiCredentials,
} from "#settings/procaptcha/api/apiCredentials.js";
import LoggerFactory from "#utils/logger/loggerFactory.js";
import PluginModuleLogger from "#utils/logger/plugin/pluginModuleLogger.js";
import {
	ApiClient,
	type ProcaptchaAccountResolver,
} from "#settings/procaptcha/api/apiClient.js";

class GeneralSettings {
	private readonly logger: Logger;
	private readonly webComponentRegistrar: WebComponentRegistrar;
	private readonly config: GeneralSettingsConfig;
	private readonly accountApiResolver: ProcaptchaAccountResolver;
	private readonly apiUser: ApiCredentials;

	public constructor() {
		const loggerFactory = new LoggerFactory();
		const pluginModuleLogger = new PluginModuleLogger();

		this.logger = loggerFactory.createLogger(
			"statistics",
			pluginModuleLogger,
		);
		this.webComponentRegistrar = new WebComponentRegistrar(this.logger);

		this.config = new GeneralSettingsConfig();

		this.accountApiResolver = new ApiClient(
			this.config.getAccountApiEndpoint(),
			this.logger,
		);

		this.apiUser = new SiteApiCredentials(
			this.config.getSiteKey(),
			this.config.getSecretKey(),
		);
	}

	public setupWebComponent(): void {
		const generalSettingsWebComponent = new GeneralSettingsWebComponent(
			this.apiUser,
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
