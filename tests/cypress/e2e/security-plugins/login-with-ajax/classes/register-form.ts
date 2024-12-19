import { RegisterForm as WPRegisterForm } from "@wordpress/register-form";

class RegisterForm extends WPRegisterForm {
	protected defineSettings() {
		super.defineSettings();

		this.selectors.successMessage = ".lwa-ajaxify-status";
		this.selectors.errorMessage = ".lwa-ajaxify-status";
		this.messages.success =
			"Registration complete. Please check your e-mail.";
	}
}

export { RegisterForm };
