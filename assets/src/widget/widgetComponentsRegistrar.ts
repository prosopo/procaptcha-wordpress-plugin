import FormValidator from "./formValidator.js";
import { WebComponentRegistrar } from "./webComponent/webComponentRegistrar.js";
import WidgetRenderer from "./widgetRenderer.js";

class WidgetComponentsRegistrar {
	public constructor(
		private readonly componentRegistrar: WebComponentRegistrar,
	) {}

	public registerComponents(
		widgetRenderer: WidgetRenderer,
		formValidator: FormValidator,
	): void {
		this.registerWidgetComponent(widgetRenderer);
		this.registerFormComponent(formValidator);
	}

	protected registerWidgetComponent(widgetRenderer: WidgetRenderer): void {
		this.componentRegistrar.registerWebComponent({
			name: "prosopo-procaptcha-wp-widget",
			componentController: widgetRenderer,
			processIfReconnected: false,
			// wait, case we need to make sure window.procaptcha is available.
			waitWindowLoadedInsteadOfDomLoaded: true,
		});
	}

	protected registerFormComponent(formValidator: FormValidator): void {
		this.componentRegistrar.registerWebComponent({
			name: "prosopo-procaptcha-wp-form",
			componentController: formValidator,
			processIfReconnected: false,
			waitWindowLoadedInsteadOfDomLoaded: false,
		});
	}
}

export { WidgetComponentsRegistrar };
