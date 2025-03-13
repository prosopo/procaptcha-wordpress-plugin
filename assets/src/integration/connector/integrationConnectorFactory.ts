import { IntegrationConnector } from "./IntegrationConnector.js";
import LoggerFactory from "../../logger/loggerFactory.js";
import PluginModuleLogger from "../../logger/plugin/pluginModuleLogger.js";
import { WebComponentRegistrar } from "../../webComponent/webComponentRegistrar.js";

function createIntegrationConnector(): IntegrationConnector {
	const loggerFactory = new LoggerFactory();
	const moduleLogger = new PluginModuleLogger();

	const componentRegistrarLogger = loggerFactory.createLogger(
		"web-component-registrar",
		moduleLogger,
	);

	const webComponentRegistrar = new WebComponentRegistrar(
		componentRegistrarLogger,
	);

	return new IntegrationConnector(
		loggerFactory,
		moduleLogger,
		webComponentRegistrar,
	);
}

export { createIntegrationConnector };
