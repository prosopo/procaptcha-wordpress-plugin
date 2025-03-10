import Logger from "../logger/logger.js";
import ComponentControllerInterface from "../interfaces/componentControllerInterface.js";

class WidgetRenderer implements ComponentControllerInterface {
	private readonly logger: Logger;

	constructor(logger: Logger) {
		this.logger = logger;
	}

	processElement(origin: HTMLElement) {
		const procaptchaServiceCallback = this.getProcaptchaServiceCallback();

		if (null === procaptchaServiceCallback) {
			this.logger.warning("Procaptcha service script is not available.");
			return;
		}

		const captchaElement = this.getCaptchaElement(origin);

		if (null === captchaElement) {
			this.logger.warning("Inner captcha container is missing.");
			return;
		}

		const captchaAttributes = this.getCaptchaAttributes(origin);

		this.logger.debug("Rendering", {
			captchaElement: captchaElement,
			captchaAttributes: captchaAttributes,
		});

		procaptchaServiceCallback(captchaElement, captchaAttributes);
	}

	public updateRelatedFormOnSuccessfulValidation(
		origin: HTMLElement,
		token: string,
	): void {
		this.logger.debug("Token received", {
			token: token,
		});

		const form = origin.closest("form");
		// Some forms (like Ninja Forms) require a special hidden input implementation, which can be placed anywhere in the form.
		// that's why always try to detect it from the form tag (if exists).
		const customInputForToken = this.getCustomInputForToken(origin, form);
		const validationElement = this.getValidationElement(origin);

		// Custom input is optional, if missing, the Procaptha's JS will add 'name=procaptcha-response' automatically.
		// The custom input is intended only on the JS-based forms, that do not send the whole form data.
		// So we must put the token value to right input manually.
		if (null !== customInputForToken) {
			this.setInputValue(customInputForToken, token);
		} else {
			this.logger.debug("No custom input found");
		}

		// allow third-party listen to this event.
		this.dispatchFilledEvent(origin, token);

		// it's fully optional.
		if (null !== validationElement) {
			this.dispatchValidationFilledEvent(validationElement);
		} else {
			this.logger.debug("No validation element found");
		}
	}

	protected getProcaptchaServiceCallback(): (
		element: HTMLElement,
		args: object,
	) => void | null {
		if (
			false === window.hasOwnProperty("procaptcha") ||
			"object" !== typeof window["procaptcha"] ||
			"function" !== typeof window["procaptcha"].render
		) {
			return null;
		}

		return window["procaptcha"].render;
	}

	protected getCaptchaElement(origin: HTMLElement): HTMLElement | null {
		return origin.querySelector(".prosopo-procaptcha");
	}

	protected getCaptchaAttributes(origin: HTMLElement): object {
		const globalAttributes =
			true === window.hasOwnProperty("procaptchaWpAttributes") &&
			"object" === typeof window["procaptchaWpAttributes"]
				? window["procaptchaWpAttributes"]
				: {};

		return Object.assign(globalAttributes, {
			callback: this.updateRelatedFormOnSuccessfulValidation.bind(
				this,
				origin,
			),
		});
	}

	protected dispatchFilledEvent(origin: HTMLElement, token: string): void {
		origin.dispatchEvent(
			new CustomEvent("_prosopo-procaptcha__filled", {
				bubbles: true,
				detail: {
					token: token,
				},
			}),
		);

		this.logger.debug("Dispatched filled event", {
			token: token,
		});
	}

	protected getCustomInputForToken(
		origin: HTMLElement,
		form: HTMLElement | null,
	): HTMLInputElement | null {
		const input =
			null !== form
				? form.querySelector(".prosopo-procaptcha-input")
				: origin.parentElement.querySelector(
						".prosopo-procaptcha-input",
					);

		return input instanceof HTMLInputElement ? input : null;
	}

	protected getValidationElement(origin: HTMLElement): HTMLElement | null {
		return origin.parentElement.querySelector(
			".prosopo-procaptcha-wp-form",
		);
	}

	protected dispatchValidationFilledEvent(
		validationElement: HTMLElement,
	): void {
		validationElement.dispatchEvent(new CustomEvent("_procaptcha-filled"));

		this.logger.debug("Dispatched validation filled event", {
			validationElement: validationElement,
		});
	}

	protected setInputValue(input: HTMLInputElement, value: string): void {
		input.value = value;

		// emulate the change event.
		input.dispatchEvent(
			new Event("change", {
				bubbles: true,
			}),
		);

		this.logger.debug("Set input value", {
			input: input,
			value: value,
		});
	}
}

export default WidgetRenderer;
