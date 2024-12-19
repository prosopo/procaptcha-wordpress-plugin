import { LoginCredentials, LoginForm } from "@wordpress/login-form";
import { Message } from "@support/form-test";

class ElementorLogin extends LoginForm {
	protected defineSettings() {
		super.defineSettings();

		this.url = "/elementor-login-widget/";
		this.isAuthSupportedByVendor = false;
		this.isClientSideFieldValidationSupported = true;
		this.selectors = {
			formWithCaptcha: ".elementor-login.elementor-form",
			formWithoutCaptcha: ".elementor-login.elementor-form",
			successMessage: "",
			errorMessage: "#login_error",
			errorFieldMessage: "",
			captchaInput: "",
		};
		this.submitValues = {
			log: LoginCredentials.USERNAME,
			pwd: LoginCredentials.PASSWORD,
		};
		this.messages = {
			success: "",
			fail: Message.VALIDATION_ERROR,
		};
	}
}

export default ElementorLogin;
