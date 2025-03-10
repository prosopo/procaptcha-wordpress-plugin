import LoggerFactory from "../logger/loggerFactory.js";
import PluginModuleLogger from "../logger/plugin/pluginModuleLogger.js";
import { WidgetComponentsRegistrar } from "./widgetComponentsRegistrar.js";
import WidgetRenderer from "./widgetRenderer.js";
import FormValidator from "./formValidator.js";

class WidgetFactory {
	public constructor(
		private readonly loggerFactory: LoggerFactory,
		private readonly moduleLogger: PluginModuleLogger,
		private readonly widgetComponentsRegistrar: WidgetComponentsRegistrar,
	) {}

	public createWidget(): void {
		const rendererLogger = this.loggerFactory.makeLogger(
			"widget-renderer",
			this.moduleLogger,
		);
		const formValidatorLogger = this.loggerFactory.makeLogger(
			"form-validator",
			this.moduleLogger,
		);

		const widgetRenderer = new WidgetRenderer(rendererLogger);
		const formValidator = new FormValidator(formValidatorLogger);

		this.widgetComponentsRegistrar.registerComponents(
			widgetRenderer,
			formValidator,
		);
	}
}

export { WidgetFactory };
