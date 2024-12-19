import { LostPasswordForm as WPLostPasswordForm } from "@wordpress/lost-password-form";

class LostPasswordForm extends WPLostPasswordForm {
	protected defineSettings() {
		super.defineSettings();

		this.url = "/bbpress-lost-password/";
		this.selectors.formWithCaptcha = ".bbp-login-form";
		this.selectors.formWithoutCaptcha = ".bbp-login-form";
	}

	// When the server validation fails, bbPress redirects to the native login page.
	protected getFailSubmitMessageSelector(): string {
		return "#login #login_error";
	}

	protected checkSuccessfulSubmit(formSelector: string, userRole: string) {
		cy.url().should(
			"contain",
			"/bbpress-lost-password/?checkemail=confirm",
		);
	}
}

export { LostPasswordForm };
