import { RegisterForm as WPRegisterForm } from "@wordpress/register-form";

class RegisterForm extends WPRegisterForm {
	protected defineSettings() {
		super.defineSettings();

		this.url = "/register";
		this.selectors.formWithCaptcha = ".tml-register";
		this.selectors.formWithoutCaptcha = ".tml-register";
		this.selectors.errorMessage = ".tml-errors";
	}

	// after a successful registration, the user is redirected to the login page.
	protected getSuccessfulSubmitMessageSelector(
		formSelector: string,
		userRole: string,
	): string {
		return ".tml-login .tml-messages";
	}
}

export { RegisterForm };
