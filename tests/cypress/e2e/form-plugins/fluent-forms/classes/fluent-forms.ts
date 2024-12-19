import { FormSelector, FormTest } from "@support/form-test";

class FluentForms extends FormTest {
	defineSettings(): void {
		super.defineSettings();

		this.url = "/fluent-forms/";
		this.isAuthSupportedByVendor = true;
		this.selectors = {
			formWithCaptcha: ".fluentform_wrapper_4",
			formWithoutCaptcha: ".fluentform_wrapper_3",
			successMessage: ".ff-message-success",
			errorMessage: "",
			errorFieldMessage: ".error.text-danger",
			captchaInput: FormSelector.CUSTOM_CAPTCHA_INPUT,
		};
		this.submitValues = {
			input_text: "John Doe",
		};
		this.messages = {
			success:
				"Thank you for your message. We will get in touch with you shortly",
			fail: "",
		};
	}
}

export default FluentForms;
