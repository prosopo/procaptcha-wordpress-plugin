import type {
	ExpectedResult,
	FormSubmitionSettings,
} from "@support/commands/submit-form";
import { CaptchaValue, Message } from "@support/form-test";

(() => {
	const pluginSlugsToActivate: string[] = ["bb-plugin"];
	let pluginSlugsToDeactivate: string[] = [];

	before(() => {
		cy.togglePlugins(true, pluginSlugsToActivate).then(
			(disabledPluginSlugs) => {
				pluginSlugsToDeactivate = disabledPluginSlugs;
			},
		);
	});

	after(() => {
		if (pluginSlugsToDeactivate) {
			cy.togglePlugins(false, pluginSlugsToDeactivate);
		}
	});
})();

const submitForm = (options: Partial<FormSubmitionSettings>) => {
	const defaults: Partial<FormSubmitionSettings> = {
		fieldValues: {
			"fl-name": "John Doe",
			"fl-email": "test@gmail.com",
			"fl-message": "Hey",
		},
		submitButtonSelector: "a.fl-button",
	};

	const settings = { ...defaults, ...options };

	cy.submitForm(settings);
};

const submissionResult = {
	successful: {
		element: {
			selector: ".fl-success-msg",
			label: "Message Sent!",
		},
	} as ExpectedResult,
	failed: {
		element: {
			selector: ".fl-send-error",
			label: Message.VALIDATION_ERROR,
		},
	} as ExpectedResult,
};

describe("Default contact form", () => {
	const page = "/beaver-contact-form/";
	const formSelector = ".fl-node-0q3pr1yu6754";

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

describe("Protected contact form", () => {
	const page = "/beaver-contact-form/";
	const formSelector = ".fl-node-5pz83b20h9vw";

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
