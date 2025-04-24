import Logger from "#logger/logger.js";
import { WebComponent } from "#webComponent/webComponent.js";

class FormIntegrationComponent implements WebComponent {
	private readonly logger: Logger;

	constructor(logger: Logger) {
		this.logger = logger;
	}

	public constructComponent(element: HTMLElement) {
		const form = element.closest("form");

		if (null === form) {
			this.logger.warning(
				"Form for setting up the validation is not found.",
				{
					element: element,
				},
			);

			return;
		}

		form.addEventListener(
			"submit",
			this.preventSubmissionWithoutToken.bind(this, element),
		);
		element.addEventListener(
			"_procaptcha-filled",
			this.setErrorElementVisibility.bind(this, element, false),
		);

		this.logger.debug("Form validation is setup", {
			element: element,
			form: form,
		});
	}

	public preventSubmissionWithoutToken(
		origin: HTMLElement,
		event: SubmitEvent,
	): void {
		const form = <HTMLFormElement>event.target;

		const isStdCaptchaResponseInputPresent =
			this.isStdCaptchaResponseInputPresent(form);
		const isCustomCaptchaResponseIsPresent =
			this.isCustomCaptchaResponseIsPresent(form);
		// Allow bypassing in tests. It's safe as always protected on the server side.
		const isBypassing = form.classList.contains(
			"prosopo-procaptcha__no-client-validation",
		);

		const logArgs = {
			isStdCaptchaResponseInputPresent: isStdCaptchaResponseInputPresent,
			isCustomCaptchaResponseIsPresent: isCustomCaptchaResponseIsPresent,
			isBypassing: isBypassing,
			origin: origin,
			form: form,
		};

		if (
			true == isStdCaptchaResponseInputPresent ||
			true === isCustomCaptchaResponseIsPresent ||
			true === isBypassing
		) {
			this.logger.debug("Submission validation is passed", logArgs);

			return;
		}

		this.logger.debug("Submission validation is failed", logArgs);

		event.preventDefault();
		event.stopPropagation();

		this.setErrorElementVisibility(origin, true);
	}

	protected isStdCaptchaResponseInputPresent(form: HTMLFormElement): boolean {
		return null !== form.querySelector('input[name="procaptcha-response"]');
	}

	protected isCustomCaptchaResponseIsPresent(form: HTMLFormElement): boolean {
		const input = form.querySelector("input.prosopo-procaptcha-input");

		return input instanceof HTMLInputElement && "" !== input.value;
	}

	public setErrorElementVisibility(
		origin: HTMLElement,
		isVisible: boolean,
	): void {
		const errorElement = this.getErrorElement(origin);

		if (null === errorElement) {
			this.logger.warning("Error element is not found.", {
				origin: origin,
			});
			return;
		}

		this.setElementVisibility(errorElement, isVisible);

		this.logger.debug("Set error element visibility", {
			isVisible: isVisible,
			origin: origin,
			errorElement: errorElement,
		});
	}

	protected getErrorElement(origin: HTMLElement): HTMLElement | null {
		return origin.querySelector(".prosopo-procaptcha-wp-form__error");
	}

	protected setElementVisibility(
		element: HTMLElement,
		isVisible: boolean,
	): void {
		element.style.visibility = true === isVisible ? "visible" : "hidden";
	}
}

export default FormIntegrationComponent;
