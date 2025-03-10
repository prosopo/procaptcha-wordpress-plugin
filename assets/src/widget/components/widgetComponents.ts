import LoggerFactory from "../../logger/loggerFactory.js";
import PluginModuleLogger from "../../logger/plugin/pluginModuleLogger.js";
import WidgetComponent from "./widgetComponent.js";
import WidgetFormComponent from "./widgetFormComponent.js";
import { WebComponentFactory } from "../webComponent/webComponentFactory.js";

class WidgetComponents {
	public constructor(
		private readonly loggerFactory: LoggerFactory,
		private readonly moduleLogger: PluginModuleLogger,
		private readonly webComponentFactory: WebComponentFactory,
	) {}

	public createWidgetComponents(): void {
		const widgetComponent = this.createWidgetComponent();
		const widgetFormComponent = this.createWidgetFormComponent();

		this.createWidgetWebComponent(widgetComponent);
		this.createWidgetFormWebComponent(widgetFormComponent);
	}

	protected createWidgetComponent(): WidgetComponent {
		const rendererLogger = this.loggerFactory.makeLogger(
			"widget-renderer",
			this.moduleLogger,
		);

		return new WidgetComponent(rendererLogger);
	}

	protected createWidgetFormComponent(): WidgetFormComponent {
		const formValidatorLogger = this.loggerFactory.makeLogger(
			"form-validator",
			this.moduleLogger,
		);

		return new WidgetFormComponent(formValidatorLogger);
	}

	protected createWidgetWebComponent(widgetRenderer: WidgetComponent): void {
		this.webComponentFactory.createWebComponent({
			name: "prosopo-procaptcha-wp-widget",
			componentClass: widgetRenderer,
			processIfReconnected: false,
			// wait, case we need to make sure window.procaptcha is available.
			waitWindowLoadedInsteadOfDomLoaded: true,
		});
	}

	protected createWidgetFormWebComponent(
		formValidator: WidgetFormComponent,
	): void {
		this.webComponentFactory.createWebComponent({
			name: "prosopo-procaptcha-wp-form",
			componentClass: formValidator,
			processIfReconnected: false,
			waitWindowLoadedInsteadOfDomLoaded: false,
		});
	}
}

export { WidgetComponents };
