import WooCheckoutClassic from "./woo-checkout-classic";
import { FormSubmitionSettings } from "@support/commands/submitForm";

const INTERCEPT_REQUEST_TIMEOUT_MS = 60 * 1000;

class WooCheckoutBlocks extends WooCheckoutClassic {
	// Less, cause the form doesn't support auth.
	itemsCountToRemove = 2;

	protected defineSettings() {
		super.defineSettings();

		this.url = "/checkout/";
		this.isAuthSupportedByVendor = false;

		this.selectors.formWithCaptcha = ".wc-block-checkout__form";
		this.selectors.formWithoutCaptcha = ".wc-block-checkout__form";
		this.selectors.errorMessage =
			".wc-block-components-notice-banner.is-error";

		this.submitValues = {
			address_1: "Street",
			city: "City",
			country: "UA",
			email: "test@gmail.com",
			first_name: "John",
			last_name: "Doe",
			postcode: "69104",
			state: "UA23",
		};
	}

	// It's out of the form.
	protected getFailSubmitMessageSelector(): string {
		return this.selectors.errorMessage;
	}

	// Do not prefix fields that expect the exact values.
	protected prefixSubmitValue(key: string, value: string): string {
		if (["country", "city", "postcode", "state"].includes(key)) {
			return value;
		}

		return super.prefixSubmitValue(key, value);
	}

	protected setCartData(wpData, billingFields): void {
		cy.intercept("POST", "/wp-json/wc/store/v1/batch*").as("cartData");

		wpData.dispatch("wc/store/cart").setBillingAddress(billingFields);

		cy.wait("@cartData", { timeout: INTERCEPT_REQUEST_TIMEOUT_MS });
	}

	protected setCaptchaValue(wpData, captchaValue: string): void {
		cy.intercept("POST", "/wp-json/wc/store/v1/checkout*").as(
			"captchaValue",
		);

		wpData.dispatch("wc/store/checkout").setAdditionalFields({
			"prosopo-procaptcha/prosopo_procaptcha": captchaValue,
		});

		// for some reason, Woo doesn't always make this request - maybe only if the value was changed
		// cy.wait("@captchaValue", { timeout: INTERCEPT_REQUEST_TIMEOUT_MS });
	}

	/**
	 * It's necessary to wait until the captcha is drawn,
	 * otherwise .setAdditionalFields() call will be passed without error but with no effect.
	 *
	 * Note: it would be better to wait generic 'additional fields ready' event, but it isn't present in the Woo's JS.
	 */
	protected waitForCaptcha(): void {
		cy.get("prosopo-procaptcha-wp-widget").should("exist");
	}

	// On the checkout page, the usual 'input.value=x' approach doesn't work.
	protected submitForm(settings: FormSubmitionSettings): void {
		let captchaValue = settings.captchaValue || "";

		/**
		 * The waiting workaround must be placed exactly before we access window.wp.data,
		 * otherwise it doesn't work.
		 */
		if (captchaValue.length > 0) {
			this.waitForCaptcha();
		}

		cy.window()
			.should("have.property", "wp")
			.then((wp) => {
				cy.wrap(wp)
					.its("data")
					.should("exist")
					.then((wpData) => {
						this.setCartData(wpData, settings.fieldValues);

						if (captchaValue.length > 0) {
							this.setCaptchaValue(wpData, captchaValue);
						}

						cy.get(
							"button.wc-block-components-checkout-place-order-button",
						).click();
					});
			});
	}
}

export default WooCheckoutBlocks;
