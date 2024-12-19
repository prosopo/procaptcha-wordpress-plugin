import { FormSelector, FormTest } from "@support/form-test";

class NinjaForms extends FormTest {
	defineSettings(): void {
		super.defineSettings();

		this.url = "/ninja-forms/";
		this.isAuthSupportedByVendor = true;
		this.selectors = {
			formWithCaptcha: "#nf-form-2-cont",
			formWithoutCaptcha: "#nf-form-1-cont",
			successMessage: ".nf-response-msg",
			errorMessage: ".nf-error-field-errors",
			errorFieldMessage: ".nf-error-required-error",
			captchaInput: FormSelector.CUSTOM_CAPTCHA_INPUT,
		};
		this.submitValues = {
			".textbox-container input[type=text]": "John Doe",
		};
		this.messages = {
			success: "Form submitted successfully.\n",
			fail: "Please correct errors before submitting this form.",
		};
	}
}

export default NinjaForms;
