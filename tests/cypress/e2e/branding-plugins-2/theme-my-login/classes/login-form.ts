import { LoginForm as WPLoginForm } from "@wordpress/login-form";

class LoginForm extends WPLoginForm {
	protected defineSettings() {
		super.defineSettings();

		this.url = "/theme-login";
		this.selectors.formWithCaptcha = ".tml-login";
		this.selectors.formWithoutCaptcha = ".tml-login";
		this.selectors.errorMessage = ".tml-errors";
	}
}

export { LoginForm };
