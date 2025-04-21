import { WebComponent } from "#webComponent/webComponent.js";
import Logger from "#logger/logger.js";

class BeaverBuilderIntegrationComponent implements WebComponent {
	private readonly logger: Logger;

	constructor(logger: Logger) {
		this.logger = logger;
	}

	constructComponent(integrationElement: HTMLElement): void {
		const contactForm = integrationElement.closest(
			".fl-module-contact-form",
		);

		// todo log for fails

		if (contactForm instanceof HTMLElement) {
			const nodeId = contactForm.dataset["node"] || "";

			if (nodeId.length > 0) {
				const $ = window.jQuery;

				if ("function" === typeof $) {
					$.ajaxPrefilter((options) => {
						// todo data is a string, parse it
						// filter target request: action = fl_builder_email
					});
				}
			}
		}
	}
}

declare global {
	interface Window {
		jQuery?: typeof jQuery;
	}
}

export { BeaverBuilderIntegrationComponent };
