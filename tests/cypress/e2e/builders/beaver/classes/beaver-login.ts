import { LoginCredentials, LoginForm } from "@wordpress/login-form";
import { Message } from "@support/form-test";

class BeaverLogin extends LoginForm {
	protected defineSettings() {
		super.defineSettings();

		this.url = "/beaver-builder/";
		this.isAuthSupportedByVendor = false;
		this.isClientSideFieldValidationSupported = false;
		this.selectors = {
			formWithCaptcha: ".fl-login-form",
			formWithoutCaptcha: ".fl-login-form",
			successMessage: "a.fl-button",
			errorMessage: ".fl-form-error-message",
			errorFieldMessage: "",
			captchaInput: "",
			submitButton: "a.fl-button",
		};
		this.submitValues = {
			"fl-login-form-name": LoginCredentials.USERNAME,
			"fl-login-form-password": LoginCredentials.PASSWORD,
		};
		this.messages = {
			success: "Logout",
			fail: Message.VALIDATION_ERROR,
		};
	}
}

export { BeaverLogin };
