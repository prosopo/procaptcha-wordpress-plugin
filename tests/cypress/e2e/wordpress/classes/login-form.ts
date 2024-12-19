import WordpressForm from "./wordpress-form";
import { Message } from "@support/form-test";

const LoginCredentials = {
	USERNAME: "procaptcha",
	PASSWORD: "procaptcha",
};

// In a separate file, as used in other integrations (e.g. bbPress).
class LoginForm extends WordpressForm {
	protected defineSettings(): void {
		super.defineSettings();

		this.url = "/wp-login.php";
		this.isAuthSupportedByVendor = false;
		this.selectors = {
			formWithCaptcha: "#login",
			formWithoutCaptcha: "#login",
			errorMessage: "#login_error",
			successMessage: "",
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

	protected getSettingName(): string {
		return "is_on_wp_login_form";
	}

	// Global selector, since the submission is redirected to the wp-login.php,
	// so no original form is available anymore.
	protected getFailSubmitMessageSelector(): string {
		return this.selectors.errorMessage;
	}

	// This case doesn't require unique values.
	protected prefixSubmitValues(submitValues: object): object {
		return submitValues;
	}
}

export { LoginForm, LoginCredentials };
