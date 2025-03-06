import { FormTest, Message } from "@support/form-test";

class WooOrderTracking extends FormTest {
	protected defineSettings() {
		super.defineSettings();

		this.url = "/woocommerce-order-tracking/";
		this.isAuthSupportedByVendor = true;
		this.isClientSideFieldValidationSupported = true;
		this.selectors = {
			formWithCaptcha: ".woocommerce-form-track-order",
			formWithoutCaptcha: ".woocommerce-form-track-order",
			successMessage: "",
			errorMessage: ".wc-block-components-notice-banner__content",
			errorFieldMessage: "",
			captchaInput: "",
		};
		this.submitValues = {
			orderid: "1131",
			order_email: "test@gmail.com",
		};
		this.messages = {
			success: "",
			fail: Message.VALIDATION_ERROR,
		};
	}

	protected toggleFeatureSupport(isActivation: boolean): void {
		this.toggleSetting("woocommerce", "is_on_order_tracking", isActivation);
	}

	// It's out of the form.
	protected getFailSubmitMessageSelector(): string {
		return this.selectors.errorMessage;
	}

	// The values must be as is.
	protected prefixSubmitValues(submitValues: object): object {
		return submitValues;
	}
}

export default WooOrderTracking;
