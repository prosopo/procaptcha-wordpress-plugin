import WordpressForm from "./wordpress-form";
import { Message } from "@support/form-test";
import { LoginCredentials } from "@wordpress/login-form";

class LostPasswordForm extends WordpressForm {
	protected defineSettings(): void {
		super.defineSettings();

		this.url = "/wp-login.php?action=lostpassword";
		this.isAuthSupportedByVendor = false;
		this.selectors = {
			formWithCaptcha: "#login",
			formWithoutCaptcha: "#login",
			errorMessage: "#login_error",
			successMessage: "#login-message",
			errorFieldMessage: "",
			captchaInput: "",
		};
		this.submitValues = {
			user_login: LoginCredentials.USERNAME,
		};
		this.messages = {
			success:
				"Check your email for the confirmation link, then visit the login page.",
			fail: Message.VALIDATION_ERROR,
		};
	}

	protected getSettingName(): string {
		return "is_on_wp_lost_password_form";
	}

	// This case doesn't require unique values.
	protected prefixSubmitValues(submitValues: object): object {
		return submitValues;
	}
}

export { LostPasswordForm };
