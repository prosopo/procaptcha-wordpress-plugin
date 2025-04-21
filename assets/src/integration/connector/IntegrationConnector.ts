import { WebComponentRegistrar } from "#webComponent/webComponentRegistrar.js";
import type LoggerFactory from "#logger/loggerFactory.js";
import type PluginModuleLogger from "#logger/plugin/pluginModuleLogger.js";
import type { Integration } from "#integration/integration.js";

class IntegrationConnector {
	public constructor(
		private readonly loggerFactory: LoggerFactory,
		private readonly moduleLogger: PluginModuleLogger,
		private readonly webComponentRegistrar: WebComponentRegistrar,
	) {}

	public connectIntegration(integration: Integration): void {
		const integrationComponentLogger = this.loggerFactory.createLogger(
			integration.name,
			this.moduleLogger,
		);

		const integrationComponent = integration.createWebComponent(
			integrationComponentLogger,
		);

		const integrationWebComponentSettings =
			integration.getWebComponentSettings();

		this.webComponentRegistrar.registerWebComponent(
			integrationComponent,
			integrationWebComponentSettings,
		);
	}
}

export { IntegrationConnector };
