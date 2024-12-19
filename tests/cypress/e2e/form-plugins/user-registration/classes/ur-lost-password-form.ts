import { LostPasswordForm as WpLostPasswordForm } from "@wordpress/lost-password-form";

class UrLostPasswordForm extends WpLostPasswordForm {
	protected defineSettings() {
		super.defineSettings();

		this.selectors.formWithCaptcha = ".ur_lost_reset_password";
		this.selectors.formWithoutCaptcha = this.selectors.formWithCaptcha;

		this.messages.success = "Password reset email has been sent.";
	}

	// It's outside of the form.
	protected getSuccessfulSubmitMessageSelector(
		formSelector: string,
		userRole: string,
	): string {
		return ".user-registration-message";
	}

	// It's outside of the form.
	protected getFailSubmitMessageSelector(): string {
		return ".user-registration-error";
	}
}

export default UrLostPasswordForm;
