import { RegisterForm as WPRegisterForm } from "@wordpress/register-form";

class RegisterForm extends WPRegisterForm {
	protected defineSettings() {
		super.defineSettings();

		this.url = "/bbpress-registration/";
		this.selectors.formWithCaptcha = ".bbp-login-form";
		this.selectors.formWithoutCaptcha = ".bbp-login-form";
	}

	// When the server validation fails, bbPress redirects to the native login page.
	protected getFailSubmitMessageSelector(): string {
		return "#login #login_error";
	}

	// When the registration completed, bbPress redirects to the native login page.
	protected getSuccessfulSubmitMessageSelector(
		formSelector: string,
		userRole: string,
	): string {
		return "#login " + this.selectors.successMessage;
	}
}

export { RegisterForm };
