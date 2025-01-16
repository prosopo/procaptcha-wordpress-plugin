import { FormSelector, FormTest, Message } from "@support/form-test";

class UserRegistration extends FormTest {
	protected defineSettings() {
		super.defineSettings();

		this.url = "/user-registration/";
		this.isAuthSupportedByVendor = false;
		this.selectors = {
			formWithCaptcha: "#user-registration-form-502",
			formWithoutCaptcha: "#user-registration-form-499",
			successMessage: ".user-registration-message",
			errorMessage: ".user-registration-error",
			errorFieldMessage: "#prosopo_procaptcha-error",
			captchaInput: FormSelector.CUSTOM_CAPTCHA_INPUT,
		};
		this.submitValues = {
			user_login: "tester",
			user_email: "tester@gmail.com",
			user_pass: "pass",
			user_confirm_password: "pass",
		};
		this.messages = {
			success: "User successfully registered.",
			fail: Message.VALIDATION_ERROR,
			failOnMissingCaptcha: "Prosopo Procaptcha is a required field.",
			fieldFail: "This field is required.",
		};
	}

	// Keep the pass confirmation equal to the password.
	protected prefixSubmitValues(submitValues: object): object {
		submitValues = super.prefixSubmitValues(submitValues);

		return Object.assign(submitValues, {
			user_confirm_password: submitValues["user_pass"] || "",
		});
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

export default UserRegistration;
