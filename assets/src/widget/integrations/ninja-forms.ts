import Logger from "../../logger/logger.js";
import { WebComponent } from "../webComponent/webComponent.js";
import { WebComponentFactory } from "../webComponent/webComponentFactory.js";
import LoggerFactory from "../../logger/loggerFactory.js";
import PluginModuleLogger from "../../logger/plugin/pluginModuleLogger.js";

class NinjaFormsIntegration implements WebComponent {
	private readonly logger: Logger;

	constructor(logger: Logger) {
		this.logger = logger;
	}

	setupComponentElement(origin: HTMLElement): void {
		const input = this.getCaptchaInput(origin);

		if (null === input) {
			this.logger.warning("Captcha input is missing");

			return;
		}

		const modelId = input.dataset["id"] || "";

		this.makeMarionetteObject(input);

		origin.parentElement
			.closest("form")
			.addEventListener("_prosopo-procaptcha__filled", () => {
				this.clearValidationError(modelId);
			});
	}

	protected getBackboneChannel(channel: string): unknown | null {
		if (
			false === window.hasOwnProperty("Backbone") ||
			false === window["Backbone"].hasOwnProperty("Radio")
		) {
			this.logger.warning("Backbone.Radio is not available");
			return null;
		}

		return window["Backbone"].Radio.channel(channel);
	}

	protected clearValidationError(modelId: unknown): void {
		const fieldsChannel = this.getBackboneChannel("fields");

		if (null === fieldsChannel) {
			this.logger.warning(
				"Can not clear validation error, as fields channel is not available",
			);
			return;
		}

		if (
			"object" === typeof fieldsChannel &&
			"request" in fieldsChannel &&
			"function" === typeof fieldsChannel.request
		) {
			this.logger.debug("Clearing validation error");

			fieldsChannel.request("remove:error", modelId, "required-error");

			return;
		}

		this.logger.warning(
			"Can not clear validation error, as fields channel does not have request method",
		);
	}

	protected getCaptchaInput(origin: HTMLElement): HTMLInputElement | null {
		const input = origin.parentElement.querySelector(
			".prosopo-procaptcha-input",
		);

		return input instanceof HTMLInputElement ? input : null;
	}

	protected makeMarionetteObject(input: HTMLInputElement): void {
		if (false === window.hasOwnProperty("Marionette")) {
			this.logger.warning("Marionette is not available");

			return;
		}

		this.logger.debug("Making Marionette object", {
			input: input,
		});

		const marionetteObject = this.getMarionetteObject(input);

		const integration =
			window["Marionette"].Object.extend(marionetteObject);

		new integration();
	}

	protected getMarionetteObject(input: HTMLInputElement): object {
		// eslint-disable-next-line @typescript-eslint/no-this-alias
		const _this = this;

		return {
			initialize() {
				_this.logger.debug("Initializing marionette object");

				const submitChannel = _this.getBackboneChannel("submit");

				this.listenTo(
					submitChannel,
					"validate:field",
					this.updateProcaptcha,
				);
			},
			updateProcaptcha(model) {
				_this.logger.debug("Update is called", {
					model: model,
				});

				const type = model.get("type");

				if ("prosopo_procaptcha" !== type) {
					return;
				}

				model.set("value", input.value);

				_this.clearValidationError(model.get("id"));
			},
		};
	}
}

const loggerFactory = new LoggerFactory();
const moduleLogger = new PluginModuleLogger();

const ninjaForms = new NinjaFormsIntegration(
	loggerFactory.makeLogger("ninja-forms", moduleLogger),
);

const componentLogger = loggerFactory.makeLogger(
	"web-component-registar",
	moduleLogger,
);

const componentRegistrar = new WebComponentFactory(componentLogger);

componentRegistrar.createWebComponent({
	name: "prosopo-procaptcha-ninja-forms-integration",
	componentClass: ninjaForms,
	processIfReconnected: false,
	waitWindowLoadedInsteadOfDomLoaded: true,
});
