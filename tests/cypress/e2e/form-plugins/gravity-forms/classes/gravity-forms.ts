import { FormSelector, FormTest } from "@support/form-test";

class GravityForms extends FormTest {
	protected defineSettings() {
		super.defineSettings();

		this.url = "/gravity-forms/";
		this.isAuthSupportedByVendor = true;
		this.selectors = {
			formWithCaptcha: "#gform_wrapper_2",
			formWithoutCaptcha: "#gform_wrapper_1",
			successMessage: ".gform_confirmation_message",
			errorMessage: ".gform_validation_errors",
			errorFieldMessage:
				".gfield--type-prosopo_procaptcha .gfield_validation_message",
			captchaInput: FormSelector.CUSTOM_CAPTCHA_INPUT,
		};
		this.submitValues = {
			"input[name=input_1][type=text]": "John Doe",
		};
		this.messages = {
			success:
				"Thanks for contacting us! We will get in touch with you shortly.",
			fail: "There was a problem with your submission. Please review the fields below.",
			fieldFail: "This field is required.",
		};
	}

	protected getSuccessfulSubmitMessageSelector(
		formSelector: string,
		userRole: string,
	): string {
		switch (formSelector) {
			case this.selectors.formWithCaptcha:
				return "#gform_confirmation_message_2";
			case this.selectors.formWithoutCaptcha:
				return "#gform_confirmation_message_1";
			default:
				return "#wrong";
		}
	}
}

export default GravityForms;
