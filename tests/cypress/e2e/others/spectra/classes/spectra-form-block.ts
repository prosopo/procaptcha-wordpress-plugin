import { FormTest } from "@support/form-test";

class SpectraFormBlock extends FormTest {
	protected defineSettings() {
		super.defineSettings();

		this.url = "/spectra-form-block/";
		this.isAuthSupportedByVendor = true;
		this.isClientSideFieldValidationSupported = false;
		this.selectors = {
			formWithCaptcha: ".form-with-captcha",
			formWithoutCaptcha: ".form-without-captcha",
			successMessage: ".uagb-forms-success-message",
			errorMessage: ".uagb-forms-failed-message",
			errorFieldMessage: "",
			captchaInput: "",
		};
		this.submitValues = {
			".uagb-forms-name-input": "name",
		};
		this.messages = {
			success: "The form has been submitted successfully!",
			fail: "Please verify all form fields again.",
		};
	}
}

export { SpectraFormBlock };
