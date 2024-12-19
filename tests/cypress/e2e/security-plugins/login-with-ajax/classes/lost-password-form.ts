import { LostPasswordForm as WPLostPasswordForm } from "@wordpress/lost-password-form";

class LostPasswordForm extends WPLostPasswordForm {
	protected defineSettings() {
		super.defineSettings();

		this.selectors.successMessage = ".lwa-ajaxify-status";
		this.selectors.errorMessage = ".lwa-ajaxify-status";
		this.messages.success = "We have sent you an email";
	}
}

export { LostPasswordForm };
