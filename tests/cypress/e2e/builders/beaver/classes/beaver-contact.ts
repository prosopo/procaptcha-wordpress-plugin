import { FormTest, Message } from "@support/form-test";
import type { ExpectedResult } from "@support/commands/submit-form";

const fieldValues = {
	"fl-name": "John Doe",
	"fl-email": "test@gmail.com",
	"fl-message": "Hey",
};

const successfulSubmissionResult: ExpectedResult = {
	element: {
		selector: ".fl-success-msg",
		label: "Message Sent!",
	},
};

describe("Default contact form", () => {
	const page = "/beaver-contact-form/";
	const formSelector = ".fl-node-0q3pr1yu6754";

	it("has no captcha field", () => {
		cy.visit(page);

		cy.assertProcaptchaExistence(false, formSelector);
	});

	it("can be submitted", () => {
		cy.visit(page);

		cy.submitForm({
			formSelector: formSelector,
			fieldValues: fieldValues,
			expectedResult: successfulSubmissionResult,
		});
	});
});

describe("Protected contact form", () => {
	const page = "/beaver-contact-form/";
	const formSelector = ".fl-node-5pz83b20h9vw";

	it("includes captcha for guests", () => {
		cy.visit(page);

		cy.assertProcaptchaExistence(true, formSelector);
	});

	it("excludes captcha for authorized", () => {
		cy.login();

		cy.visit(page);

		cy.assertProcaptchaExistence(false, formSelector);

		cy.submitForm({
			formSelector: formSelector,
			fieldValues: fieldValues,
			expectedResult: successfulSubmissionResult,
		});
	});
});

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
