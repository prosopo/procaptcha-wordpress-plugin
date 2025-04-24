import { FormTest } from "@support/form-test";

class ElementorForms extends FormTest {
	protected defineSettings() {
		super.defineSettings();

		this.url = "/elementor-forms/";
		this.isAuthSupportedByVendor = true;
		this.isClientSideFieldValidationSupported = false;
		this.selectors = {
			formWithCaptcha: ".elementor-element-0744847",
			formWithoutCaptcha: ".elementor-element-3ad5c82",
			successMessage: ".elementor-message-success",
			errorMessage: "form > .elementor-message-danger",
			errorFieldMessage: ".elementor-error .elementor-message-danger",
			captchaInput: "",
		};
		this.submitValues = {
			'[name="form_fields[name]"]': "John Doe",
		};
		this.messages = {
			success: "Your submission was successful.",
			fail: "Your submission failed because of an error.",
		};
	}
}

export default ElementorForms;
