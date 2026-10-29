import { WebComponent } from "../../utils/webComponent/webComponent.js";
import type Logger from "../../utils/logger/logger.js";
import {
	LEGACY_FILLED_EVENT,
	WIDGET_EVENTS,
} from "#integrations/procaptcha/widgetEvents.js";

declare global {
	interface Window {
		procaptcha: {
			render: (element: HTMLElement, args: object) => unknown;
			reset?: (widgetId?: string) => unknown;
		};
		procaptchaWpAttributes: object;
	}
}

class WidgetIntegrationComponent implements WebComponent {
	private readonly logger: Logger;

	constructor(logger: Logger) {
		this.logger = logger;
	}

	constructComponent(integrationElement: HTMLElement) {
		const procaptchaServiceCallback = this.getProcaptchaServiceCallback();

		if (null === procaptchaServiceCallback) {
			this.logger.warning("Procaptcha service script is not available.");
			return;
		}

		const captchaElement = this.getCaptchaElement(integrationElement);

		if (null === captchaElement) {
			this.logger.warning("Inner captcha container is missing.");
			return;
		}

		const captchaAttributes = this.getCaptchaAttributes(integrationElement);

		this.logger.debug("Rendering", {
			captchaElement: captchaElement,
			captchaAttributes: captchaAttributes,
		});

		// Recent service versions return a promise with the widget id, older ones return nothing.
		const widgetId = Promise.resolve(
			procaptchaServiceCallback(captchaElement, captchaAttributes),
		);

		// Capture phase, so it also catches non-bubbling events: the target may be the widget,
		// its form, any other ancestor, or the document (to reset every widget).
		document.addEventListener(
			WIDGET_EVENTS.reset,
			(event: Event) => {
				const target = event.target;

				if (
					target === document ||
					(target instanceof Node &&
						target.contains(integrationElement))
				) {
					widgetId.then((id) => this.resetWidget(id));
				}
			},
			true,
		);
	}

	/**
	 * Tokens can be verified only once, so forms that submit without a page reload
	 * must reset the widget after each submission, to let the visitor get a new one.
	 */
	protected resetWidget(widgetId: unknown): void {
		const reset = window.procaptcha?.reset;

		if ("string" !== typeof widgetId || "function" !== typeof reset) {
			this.logger.warning("Widget reset is not supported", {
				widgetId: widgetId,
			});
			return;
		}

		this.logger.debug("Resetting widget", {
			widgetId: widgetId,
		});

		reset(widgetId);
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

	protected getProcaptchaServiceCallback():
		| ((element: HTMLElement, args: object) => unknown)
		| null {
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
		const globalAttributes: Record<string, unknown> =
			true === window.hasOwnProperty("procaptchaWpAttributes") &&
			"object" === typeof window["procaptchaWpAttributes"]
				? (window["procaptchaWpAttributes"] as Record<string, unknown>)
				: {};

		const clearToken = this.clearRelatedFormToken.bind(this, origin);

		// Copy, so each widget keeps its own callbacks: the widget stores the options and reuses them on reset.
		return {
			...globalAttributes,
			callback: this.chainCallback(
				this.updateRelatedFormOnSuccessfulValidation.bind(this, origin),
				globalAttributes["callback"],
			),
			"expired-callback": this.chainCallback(
				clearToken,
				globalAttributes["expired-callback"],
			),
			"error-callback": this.chainCallback(
				clearToken,
				globalAttributes["error-callback"],
			),
			"reset-callback": this.chainCallback(
				clearToken,
				globalAttributes["reset-callback"],
			),
		};
	}

	/**
	 * Keeps callbacks set via the 'prosopo/procaptcha/captcha_attributes' filter working:
	 * those are global function names, as PHP can't pass functions.
	 */
	protected chainCallback<Args extends unknown[]>(
		pluginCallback: (...args: Args) => void,
		userCallback: unknown,
	): (...args: Args) => void {
		return (...args: Args) => {
			pluginCallback(...args);

			const userFunction = this.resolveUserCallback(userCallback);

			if (null !== userFunction) {
				userFunction(...args);
			}
		};
	}

	protected resolveUserCallback(
		userCallback: unknown,
	): ((...args: unknown[]) => unknown) | null {
		if ("function" === typeof userCallback) {
			return userCallback as (...args: unknown[]) => unknown;
		}

		if ("string" !== typeof userCallback || "" === userCallback) {
			return null;
		}

		const name = userCallback.replace(/^window\./, "");
		const windowCallback = (window as unknown as Record<string, unknown>)[
			name
		];

		if ("function" !== typeof windowCallback) {
			this.logger.warning("User callback is not a function", {
				name: name,
			});

			return null;
		}

		return windowCallback as (...args: unknown[]) => unknown;
	}

	/**
	 * Called when the token expires, the widget errors or is reset:
	 * the Procaptcha's JS removes its own input, but the custom one must be cleared manually,
	 * otherwise JS-based forms keep submitting a token that the server will reject.
	 */
	public clearRelatedFormToken(origin: HTMLElement): void {
		const form = origin.closest("form");
		const customInputForToken = this.getCustomInputForToken(origin, form);

		this.logger.debug("Token cleared");

		if (null !== customInputForToken && "" !== customInputForToken.value) {
			this.setInputValue(customInputForToken, "");
		}

		// allow third-party listen to this event.
		origin.dispatchEvent(
			new CustomEvent(WIDGET_EVENTS.cleared, {
				bubbles: true,
			}),
		);
	}

	protected dispatchFilledEvent(origin: HTMLElement, token: string): void {
		for (const eventName of [WIDGET_EVENTS.filled, LEGACY_FILLED_EVENT]) {
			origin.dispatchEvent(
				new CustomEvent(eventName, {
					bubbles: true,
					detail: {
						token: token,
					},
				}),
			);
		}

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
				: origin.parentElement?.querySelector(
						".prosopo-procaptcha-input",
					);

		return input instanceof HTMLInputElement ? input : null;
	}

	protected getValidationElement(origin: HTMLElement): HTMLElement | null {
		return (
			origin.parentElement?.querySelector(
				".prosopo-procaptcha-wp-form",
			) || null
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

export default WidgetIntegrationComponent;
