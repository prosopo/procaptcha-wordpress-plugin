import { FormSelector, FormTest } from "@support/form-test";

class WpForms extends FormTest {
	defineSettings(): void {
		super.defineSettings();

		this.url = "/wpforms/";
		this.isAuthSupportedByVendor = true;
		this.selectors = {
			formWithCaptcha: "#wpforms-247",
			formWithoutCaptcha: "#wpforms-241",
			successMessage: ".wpforms-confirmation-scroll",
			errorMessage: ".wpforms-error-container",
			errorFieldMessage: ".wpforms-error",
			captchaInput: FormSelector.CUSTOM_CAPTCHA_INPUT,
		};
		this.submitValues = {
			"input[type=text].wpforms-field-required": "John Doe",
		};
		this.messages = {
			success:
				"Thanks for contacting us! We will be in touch with you shortly.",
			fail: "Form has not been submitted, please see the errors below.",
		};
	}
}

export default WpForms;
