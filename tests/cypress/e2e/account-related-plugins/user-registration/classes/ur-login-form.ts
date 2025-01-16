import {
	LoginCredentials,
	LoginForm as WpLoginForm,
} from "@wordpress/login-form";

class UrLoginForm extends WpLoginForm {
	protected defineSettings() {
		super.defineSettings();

		this.submitValues = {
			username: LoginCredentials.USERNAME,
			password: LoginCredentials.PASSWORD,
		};

		this.selectors.formWithCaptcha = ".user-registration-form-login";
		this.selectors.formWithoutCaptcha = this.selectors.formWithCaptcha;
	}

	// It's outside of the form.
	protected getFailSubmitMessageSelector(): string {
		return ".user-registration-error";
	}
}

export default UrLoginForm;
