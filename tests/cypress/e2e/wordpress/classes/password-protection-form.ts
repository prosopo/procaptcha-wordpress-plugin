import WordpressForm from "./wordpress-form";
import { Message } from "@support/form-test";

class PasswordProtectionForm extends WordpressForm {
	protected defineSettings(): void {
		super.defineSettings();

		this.isAuthSupportedByVendor = true;
		this.selectors = {
			formWithCaptcha: ".post-password-form",
			formWithoutCaptcha: ".post-password-form",
			errorMessage: ".wp-die-message",
			successMessage: "",
			errorFieldMessage: "",
			captchaInput: "",
		};
		this.submitValues = {
			post_password: "password",
		};
		this.messages = {
			success: "",
			fail: Message.VALIDATION_ERROR,
		};
	}

	protected getSettingName(): string {
		return "is_on_wp_post_form";
	}

	// This case doesn't require unique values.
	protected prefixSubmitValues(submitValues: object): object {
		return submitValues;
	}

	// It's a wp_die() message, so the selector is global.
	protected getFailSubmitMessageSelector(): string {
		return this.selectors.errorMessage;
	}
}

export { PasswordProtectionForm };
