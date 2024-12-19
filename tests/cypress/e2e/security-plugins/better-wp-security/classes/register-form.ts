import { RegisterForm as WPRegisterForm } from "@wordpress/register-form";

class RegisterForm extends WPRegisterForm {
	protected defineSettings() {
		super.defineSettings();

		this.url = "/wp-login.php?action=register&itsec-hb-token=wpregister";
		// on success, SolidSecurity doesn't show the message, but redirects.
		this.messages.success = "";
	}
}

export { RegisterForm };
