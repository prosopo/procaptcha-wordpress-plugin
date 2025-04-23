import { FormTest } from "@support/form-test";

class BeaverContact extends FormTest {
	protected defineSettings() {
		super.defineSettings();

		this.url = "/beaver-builder/";
		this.isAuthSupportedByVendor = false;
		this.isClientSideFieldValidationSupported = false;
		// todo
		this.selectors = {
			formWithCaptcha: "",
			formWithoutCaptcha: "",
			successMessage: "",
			errorMessage: "",
			errorFieldMessage: "",
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

export { BeaverContact };
