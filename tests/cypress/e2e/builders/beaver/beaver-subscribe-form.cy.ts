import type {
	ExpectedResult,
	FormSubmitionSettings,
} from "@support/commands/submitForm";
import { CaptchaValue, Message } from "@support/form-test";
import { activatePluginsForTestLifetime } from "@support/pluginsManagement";

const submitForm = (settings: FormSubmitionSettings) =>
	cy.submitForm({
		fieldValues: {
			"fl-subscribe-form-name": "John Doe",
			"fl-subscribe-form-email": "test@gmail.com",
		},
		submitButtonSelector: "a.fl-button",
		...settings,
	});

const submissionResult = {
	successful: {
		element: {
			// todo change when the mailchimp account verification issue is resolved
			selector: ".fl-form-error-message",
			label: "There was an error subscribing to MailChimp",
		},
	} as ExpectedResult,
	failed: {
		element: {
			selector: ".fl-form-error-message",
			label: Message.VALIDATION_ERROR,
		},
	} as ExpectedResult,
};

activatePluginsForTestLifetime(["bb-plugin"]);

describe("Protected subscribe form", () => {
	const page = "/beaver-subscribe-form/";
	const formSelector = ".fl-node-tbzc6iuhvm5p";

	context("for guests", () => {
		beforeEach(() => cy.visit(page));

		it("has captcha", () =>
			cy.assertProcaptchaExistence(true, formSelector));

		it("can not be submitted without token", () =>
			submitForm({
				formSelector: formSelector,
				expectedResult: submissionResult.failed,
			}));

		it("can not be submitted with wrong token", () =>
			submitForm({
				formSelector: formSelector,
				captchaValue: CaptchaValue.WRONG,
				expectedResult: submissionResult.failed,
			}));

		it("can be submitted with right token", () =>
			submitForm({
				formSelector: formSelector,
				captchaValue: CaptchaValue.RIGHT,
				expectedResult: submissionResult.successful,
			}));
	});

	context("for authorized", () => {
		beforeEach(() => {
			cy.login();
			cy.visit(page);
		});

		it("does not have captcha", () =>
			cy.assertProcaptchaExistence(false, formSelector));

		it("can be submitted without token", () =>
			submitForm({
				formSelector: formSelector,
				expectedResult: submissionResult.successful,
			}));
	});
});

describe("Default subscribe form is not affected", () => {
	const page = "/beaver-subscribe-form/";
	const formSelector = ".fl-node-lzsrofqca4b2";

	context("for guests", () => {
		beforeEach(() => cy.visit(page));

		it("does not have captcha", () =>
			cy.assertProcaptchaExistence(false, formSelector));

		it("can be submitted without token", () =>
			submitForm({
				formSelector: formSelector,
				expectedResult: submissionResult.successful,
			}));
	});

	context("for authorized", () => {
		beforeEach(() => {
			cy.login();
			cy.visit(page);
		});

		it("does not have captcha", () =>
			cy.assertProcaptchaExistence(false, formSelector));

		it("can be submitted without token", () =>
			submitForm({
				formSelector: formSelector,
				expectedResult: submissionResult.successful,
			}));
	});
});
