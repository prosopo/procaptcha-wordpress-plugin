import { FormTest, Message } from "@support/form-test";

class WooCheckoutClassic extends FormTest {
	protected itemsCountToRemove: number = 4;

	protected defineSettings() {
		super.defineSettings();

		this.url = "/checkout-classic/";
		this.isAuthSupportedByVendor = true;
		this.isClientSideFieldValidationSupported = false;
		this.selectors = {
			formWithCaptcha: "form.woocommerce-checkout",
			formWithoutCaptcha: "form.woocommerce-checkout",
			successMessage: "",
			errorMessage: ".wc-block-components-notice-banner__content",
			errorFieldMessage: "",
			captchaInput: "",
		};
		this.submitValues = {
			billing_first_name: "John",
			billing_last_name: "Doe",
			billing_address_1: "Street",
			billing_country: "UA",
			billing_city: "City",
			billing_state: "UA23",
			billing_postcode: "69104",
			billing_email: "test@gmail.com",
		};
		this.messages = {
			success: "",
			fail: Message.VALIDATION_ERROR,
		};
	}

	// Do not prefix fields that expect the exact values.
	protected prefixSubmitValue(key: string, value: string): string {
		if (
			["billing_country", "billing_state", "billing_postcode"].includes(
				key,
			)
		) {
			return value;
		}

		return super.prefixSubmitValue(key, value);
	}

	protected visitTargetPage() {
		// Add the test product to cart before visiting the target page.
		// Do not add "?add-to-card" directly to the primary url,
		// as it causes the "Sorry, your session has expired" issue:
		// https://github.com/woocommerce/woocommerce-gateway-stripe/issues/551
		cy.visit("/cart/?add-to-cart=591");

		super.visitTargetPage();
	}

	protected toggleFeatureSupport(isActivation: boolean): void {
		this.toggleSetting("woocommerce", "is_on_checkout", isActivation);
	}

	protected afterScenario() {
		super.afterScenario();

		it("removeOrders", () => {
			this.integrationTest.login();

			cy.visit("/wp-admin/admin.php?page=wc-orders");

			for (let i = 0; i < this.itemsCountToRemove; i++) {
				cy.get('.type-shop_order .check-column input[type="checkbox"]')
					.eq(i)
					.check();
			}

			cy.get("#bulk-action-selector-top").select("trash");
			cy.get("#doaction").click();

			cy.visit("/wp-admin/admin.php?page=wc-orders&status=trash");

			cy.get("#delete_all").click();
		});
	}
}

export default WooCheckoutClassic;
