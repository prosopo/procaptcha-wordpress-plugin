import { FormSelector, FormTest } from "@support/form-test";

class EverestForms extends FormTest {
	protected defineSettings() {
		super.defineSettings();

		this.url = "/everest-forms/";
		this.isAuthSupportedByVendor = true;
		this.selectors = {
			formWithCaptcha: ".ef-form-with-captcha",
			formWithoutCaptcha: ".ef-form-without-captcha",
			successMessage: ".everest-forms-notice--success",
			errorMessage: ".everest-forms-notice--error",
			errorFieldMessage: ".evf-error",
			captchaInput: FormSelector.CUSTOM_CAPTCHA_INPUT,
		};
		this.submitValues = {
			"input.input-text[required]": "Jon Doe",
		};
		this.messages = {
			success:
				"Thanks for contacting us! We will be in touch with you shortly",
			fail: "Form has not been submitted, please see the errors below.",
		};
	}
}

export default EverestForms;
