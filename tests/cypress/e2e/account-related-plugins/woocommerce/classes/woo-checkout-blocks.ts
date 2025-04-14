import WooCheckoutClassic from "./woo-checkout-classic";
import { Settings as SubmitFormSettings } from "@support/commands/submit-form";

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

		cy.wait("@cartData");
	}

	protected setCaptchaValue(wpData, captchaValue: string): void {
		cy.intercept("POST", "/wp-json/wc/store/v1/checkout*").as(
			"captchaValue",
		);

		// fixme at this point, it has no effect:
		//  in admin it shows 'this order is no longer editable'
		wpData.dispatch("wc/store/checkout").setAdditionalFields({
			"prosopo-procaptcha/prosopo_procaptcha": captchaValue,
		});

		cy.wait("@captchaValue");
	}

	protected fillOutForm(wpData, settings: SubmitFormSettings): void {
		let captchaValue = settings.captchaValue || "";

		this.setCartData(wpData, settings.fieldValues);

		if ("" !== captchaValue) {
			this.setCaptchaValue(wpData, captchaValue);
		}
	}

	// On the checkout page, the usual 'input.value=x' approach doesn't work.
	protected submitForm(settings: SubmitFormSettings): void {
		cy.window()
			.should("have.property", "wp") // Wait for `wp` to exist
			.then((wp) => {
				cy.wrap(wp)
					.its("data") // Wait for `wp.data` to exist
					.should("exist")
					.then((wpData) => {
						this.fillOutForm(wpData, settings);

						cy.get(
							"button.wc-block-components-checkout-place-order-button",
						).click();
					});
			});
	}
}

export default WooCheckoutBlocks;
