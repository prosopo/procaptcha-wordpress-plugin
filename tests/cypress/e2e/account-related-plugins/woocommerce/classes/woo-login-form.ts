import { LoginCredentials, LoginForm } from "@wordpress/login-form";
import { Message } from "@support/form-test";

class WooLoginForm extends LoginForm {
	protected defineSettings() {
		super.defineSettings();

		this.url = "/my-account/";
		this.selectors = {
			formWithCaptcha: ".woocommerce-form-login",
			formWithoutCaptcha: ".woocommerce-form-login",
			errorMessage: "",
			successMessage: "",
			errorFieldMessage: "",
			captchaInput: "",
		};
		this.submitValues = {
			username: LoginCredentials.USERNAME,
			password: LoginCredentials.PASSWORD,
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

export default WooLoginForm;
