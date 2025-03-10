import { IntegrationComponent } from "../../../integrationComponent.js";
import Logger from "../../../../logger/logger.js";

class WooBlocksCheckoutIntegrationComponent implements IntegrationComponent {
	private readonly logger: Logger;

	constructor(logger: Logger) {
		this.logger = logger;
	}

	setupIntegrationElement(integrationElement: HTMLElement): void {
		const form = integrationElement.closest("form");

		// add a stub to bypass Woo client validation, and run server,
		// otherwise it's confusing as the input is hidden.
		this.updateInputValue("default");

		form.addEventListener(
			"_prosopo-procaptcha__filled",
			(event: CustomEvent) => {
				this.updateInputValue(event.detail.token);
			},
		);
	}

	protected updateInputValue(token: string): void {
		if (
			false === window.hasOwnProperty("wp") ||
			false === window["wp"].hasOwnProperty("data")
		) {
			this.logger.warning("window.wp.data is not available");
			return;
		}

		window["wp"].data.dispatch("wc/store/checkout").setAdditionalFields({
			"prosopo-procaptcha/prosopo_procaptcha": token,
		});
	}
}

export { WooBlocksCheckoutIntegrationComponent };
