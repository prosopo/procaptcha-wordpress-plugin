import { LoginForm as WPLoginForm } from "@wordpress/login-form";

class LoginForm extends WPLoginForm {
	protected defineSettings() {
		super.defineSettings();

		this.url = "/bbpress-login/";
		this.selectors.formWithCaptcha = ".bbp-login-form";
		this.selectors.formWithoutCaptcha = ".bbp-login-form";
	}

	// When the server validation fails, bbPress redirects to the native login page.
	protected getFailSubmitMessageSelector(): string {
		return "#login #login_error";
	}
}

export { LoginForm };
