import { WebComponent } from "#webComponent/webComponent.js";
import Logger from "#logger/logger.js";

class BeaverBuilderIntegrationComponent implements WebComponent {
	private readonly logger: Logger;

	constructor(logger: Logger) {
		this.logger = logger;
	}

	constructComponent(integrationElement: HTMLElement): void {
		const formElement = integrationElement.closest(
			".fl-module-contact-form",
		);

		if (formElement instanceof HTMLElement) {
			const formId = formElement.dataset["node"] || "";

			if (formId.length > 0) {
				this.bindFormField(formId, "procaptcha-response", () => {
					return this.getProcaptchaToken(formElement);
				});
			} else {
				this.logger.warning("Cannot get form id", {
					formElement: formElement,
				});
			}

			return;
		}

		this.logger.warning("Cannot get form element", {
			integrationElement: integrationElement,
		});
	}

	protected bindFormField(
		formId: string,
		fieldName: string,
		getFieldValue: () => string,
	): void {
		this.addAjaxRequestPrefilter((requestFields: URLSearchParams) => {
			if (
				"fl_builder_email" === requestFields.get("action") &&
				formId === requestFields.get("node_id")
			) {
				requestFields.set(fieldName, getFieldValue());
			}
		});
	}

	protected addAjaxRequestPrefilter(
		prefilterAjaxRequest: (requestFields: URLSearchParams) => void,
	): void {
		const $ = window.jQuery;

		if ("function" === typeof $) {
			$.ajaxPrefilter((options) => {
				const requestData = options.data;

				if ("string" === typeof requestData) {
					const requestFields = new URLSearchParams(requestData);

					prefilterAjaxRequest(requestFields);

					options.data = requestFields.toString();
				}
			});

			return;
		}

		this.logger.warning(
			"Cannot attach ajax prefilter: jQuery is not available",
		);
	}

	protected getProcaptchaToken(formElement: HTMLElement): string {
		const tokenInputElement = formElement.querySelector(
			`input[name=procaptcha-response]`,
		);

		if (tokenInputElement instanceof HTMLInputElement) {
			return tokenInputElement.value;
		}

		this.logger.warning("Cannot get token input element", {
			formElement: formElement,
		});

		return "";
	}
}

declare global {
	interface Window {
		jQuery?: typeof jQuery;
	}
}

export { BeaverBuilderIntegrationComponent };
