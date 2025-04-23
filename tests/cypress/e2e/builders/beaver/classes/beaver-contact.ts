import { FormTest, Message } from "@support/form-test";

class BeaverContact extends FormTest {
	protected defineSettings() {
		super.defineSettings();

		this.url = "/beaver-contact-form/";
		this.isAuthSupportedByVendor = false;
		this.isClientSideFieldValidationSupported = false;
		this.selectors = {
			formWithCaptcha: ".fl-node-5pz83b20h9vw",
			formWithoutCaptcha: ".fl-node-0q3pr1yu6754",
			successMessage: ".fl-success-msg",
			errorMessage: ".fl-send-error",
			errorFieldMessage: "",
			captchaInput: "",
			submitButton: "a.fl-button",
		};
		this.submitValues = {
			"fl-name": "John Doe",
			"fl-email": "test@gmail.com",
			"fl-message": "Hey",
		};
		this.messages = {
			success: "Message Sent!",
			fail: Message.VALIDATION_ERROR,
		};
	}
}

export { BeaverContact };
