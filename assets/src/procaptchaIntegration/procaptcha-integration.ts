import LoggerFactory from "../logger/loggerFactory.js";
import PluginModuleLogger from "../logger/plugin/pluginModuleLogger.js";
import { WebComponentRegistrar } from "./webComponent/webComponentRegistrar.js";
import { FormIntegration } from "./formIntegration/formIntegration.js";
import { WidgetIntegration } from "./widgetIntegration/widgetIntegration.js";

class ProcaptchaIntegration {
	private readonly loggerFactory: LoggerFactory;
	private readonly moduleLogger: PluginModuleLogger;
	private readonly webComponentRegistrar: WebComponentRegistrar;

	public constructor() {
		this.loggerFactory = new LoggerFactory();
		this.moduleLogger = new PluginModuleLogger();

		const componentLogger = this.loggerFactory.makeLogger(
			"web-component-registrar",
			this.moduleLogger,
		);

		this.webComponentRegistrar = new WebComponentRegistrar(componentLogger);
	}

	public setupIntegration(): void {
		this.setupWidgetIntegration();
		this.setupFormIntegration();
	}

	protected setupWidgetIntegration(): void {
		const widgetIntegration = new WidgetIntegration(
			this.loggerFactory,
			this.moduleLogger,
			this.webComponentRegistrar,
		);

		const widgetIntegrationComponent =
			widgetIntegration.createWidgetIntegrationComponent();

		widgetIntegration.registerWidgetIntegrationComponent(
			widgetIntegrationComponent,
		);
	}

	protected setupFormIntegration(): void {
		const formIntegration = new FormIntegration(
			this.loggerFactory,
			this.moduleLogger,
			this.webComponentRegistrar,
		);

		const formIntegrationComponent =
			formIntegration.createFormIntegrationComponent();

		formIntegration.registerFormIntegrationComponent(
			formIntegrationComponent,
		);
	}
}

new ProcaptchaIntegration().setupIntegration();
