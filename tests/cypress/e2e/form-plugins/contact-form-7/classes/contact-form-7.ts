import { FormTest } from "@support/form-test";

class ContactForm7 extends FormTest {
	protected defineSettings() {
		super.defineSettings();

		this.url = "/contact-form-7/";
		this.isAuthSupportedByVendor = false;
		this.selectors = {
			formWithCaptcha: "#wpcf7-f16-p17-o1 form",
			formWithoutCaptcha: "#wpcf7-f19-p17-o2 form",
			successMessage: ".wpcf7-response-output",
			errorMessage: ".wpcf7-response-output",
			errorFieldMessage:
				".wpcf7-form-control-wrap[data-name=prosopo_procaptcha] .wpcf7-not-valid-tip",
			captchaInput: "",
		};
		this.submitValues = {
			"your-name": "John Doe",
		};
		this.messages = {
			success: "Thank you for your message. It has been sent.",
			fail: "One or more fields have an error. Please check and try again.",
		};
	}
}

export default ContactForm7;
