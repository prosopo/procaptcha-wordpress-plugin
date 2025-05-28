import { FormSelector, FormTest } from "@support/form-test";

class FormidableForms extends FormTest {
	defineSettings(): void {
		super.defineSettings();

		this.url = "/formidable-forms/";
		this.isAuthSupportedByVendor = true;
		this.selectors = {
			formWithCaptcha: "#frm_form_2_container",
			formWithoutCaptcha: "#frm_form_1_container",
			successMessage: ".frm_message",
			errorMessage: ".frm_error_style",
			errorFieldMessage: "",
			captchaInput: FormSelector.CUSTOM_CAPTCHA_INPUT,
		};
		this.submitValues = {
			"input[type=text][name*=item_meta]:not(.frm_verify)": "John Doe",
		};
		this.messages = {
			success: "Your responses were successfully submitted. Thank you!\n",
			fail: "\n\tThere was a problem with your submission. Errors are marked below.",
		};
	}
}

export default FormidableForms;
