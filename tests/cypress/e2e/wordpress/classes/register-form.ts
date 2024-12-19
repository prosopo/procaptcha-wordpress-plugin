import WordpressForm from "./wordpress-form";
import { Message } from "@support/form-test";

class RegisterForm extends WordpressForm {
	protected defineSettings(): void {
		super.defineSettings();

		this.url = "/wp-login.php?action=register";
		this.isAuthSupportedByVendor = false;
		this.selectors = {
			formWithCaptcha: "#login",
			formWithoutCaptcha: "#login",
			errorMessage: "#login_error",
			successMessage: "#login-message",
			errorFieldMessage: "",
			captchaInput: "",
		};
		this.submitValues = {
			user_login: "new",
			user_email: "new@new.com",
		};
		this.messages = {
			success:
				"Registration complete. Please check your email, then visit the login page.",
			fail: Message.VALIDATION_ERROR,
		};
	}

	protected getSettingName(): string {
		return "is_on_wp_register_form";
	}

	protected afterScenario() {
		super.afterScenario();

		it("removeAddedUsers", () => {
			cy.removeUsers({
				countToRemove: 2,
			});
		});
	}
}

export { RegisterForm };
