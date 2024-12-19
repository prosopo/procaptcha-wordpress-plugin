import { LostPasswordForm as WPLostPasswordForm } from "@wordpress/lost-password-form";

class LostPasswordForm extends WPLostPasswordForm {
	protected defineSettings() {
		super.defineSettings();

		this.url = "/lostpassword";
		this.selectors.formWithCaptcha = ".tml-lostpassword";
		this.selectors.formWithoutCaptcha = ".tml-lostpassword";
		this.selectors.errorMessage = ".tml-errors";
	}

	// after a successful reset, the user is redirected to the login page.
	protected getSuccessfulSubmitMessageSelector(
		formSelector: string,
		userRole: string,
	): string {
		return ".tml-login .tml-messages";
	}
}

export { LostPasswordForm };
