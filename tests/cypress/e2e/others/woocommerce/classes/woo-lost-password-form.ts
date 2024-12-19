import { LoginCredentials } from "@wordpress/login-form";
import { Message } from "@support/form-test";
import { LostPasswordForm } from "@wordpress/lost-password-form";

class WooLostPasswordForm extends LostPasswordForm {
	protected defineSettings() {
		super.defineSettings();

		this.url = "/my-account/lost-password/";
		this.selectors = {
			formWithCaptcha: ".lost_reset_password",
			formWithoutCaptcha: ".lost_reset_password",
			errorMessage: "",
			successMessage: "",
			errorFieldMessage: "",
			captchaInput: "",
		};
		this.submitValues = {
			user_login: LoginCredentials.USERNAME,
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

export default WooLostPasswordForm;
