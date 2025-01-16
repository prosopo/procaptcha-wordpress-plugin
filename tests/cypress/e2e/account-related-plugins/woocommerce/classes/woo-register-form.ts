import { Message } from "@support/form-test";
import { RegisterForm } from "@wordpress/register-form";

class WooRegisterForm extends RegisterForm {
	protected defineSettings() {
		super.defineSettings();

		this.url = "/my-account/";
		this.selectors = {
			formWithCaptcha: ".woocommerce-form-register",
			formWithoutCaptcha: ".woocommerce-form-register",
			errorMessage: "",
			successMessage: "",
			errorFieldMessage: "",
			captchaInput: "",
		};
		this.submitValues = {
			email: "test@gmail.com",
			password: "wer$5tR3#lker03E@",
		};
		this.messages = {
			success: "",
			fail: Message.VALIDATION_ERROR,
		};
	}

	// The element is out of the form.
	protected getFailSubmitMessageSelector(): string {
		return ".wc-block-components-notice-banner.is-error";
	}
}

export default WooRegisterForm;
