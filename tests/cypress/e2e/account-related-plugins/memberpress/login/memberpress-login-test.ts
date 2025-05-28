import type {
	ExpectedResult,
	FormSubmitionSettings,
} from "@support/commands/submitForm";
import { LoginCredentials } from "@wordpress/login-form";
import { activatePluginsForTestLifetime } from "@support/pluginsManagement";
import { toggleProcaptchaOption } from "@support/options";
import { CaptchaValue, FieldError } from "@support/form-test";

const submitForm = (settings: FormSubmitionSettings) =>
	cy.submitForm({
		fieldValues: {
			log: LoginCredentials.USERNAME,
			pwd: LoginCredentials.PASSWORD,
		},
		submitButtonSelector: "#wp-submit",
		...settings,
	});

const successfulSubmissionResult = {
	element: {
		selector: "#mepr_loginform",
		shouldBeMissing: true,
	},
} as ExpectedResult;

const formSelector = "#mepr_loginform";

activatePluginsForTestLifetime(["memberpress-pro"]);

describe("login form", () => {
	// beforeEach(() => cy.visit(pageUrl)); it's present in the end test file.

	context("not protected by default", () => {
		it("does not have captcha", () =>
			cy.assertProcaptchaExistence(false, formSelector));

		it("can be submitted without token", () =>
			submitForm({
				formSelector: formSelector,
				expectedResult: successfulSubmissionResult,
			}));
	});

	context("protected when enabled", () => {
		const toggleProtection = (isEnabled: boolean) =>
			toggleProcaptchaOption(
				"account-forms",
				"is_on_wp_login_form",
				isEnabled,
			);

		before(() => {
			toggleProtection(true);
		});

		after(() => {
			toggleProtection(false);
		});

		it("has captcha", () =>
			cy.assertProcaptchaExistence(true, formSelector));

		it("can not be submitted without token", () =>
			submitForm({
				formSelector: formSelector,
				expectedResult: {
					element: {
						selector: FieldError.SELECTOR,
						label: FieldError.LABEL,
					},
				},
			}));

		it("can not be submitted with wrong token", () =>
			submitForm({
				formSelector: formSelector,
				captchaValue: CaptchaValue.WRONG,
				expectedResult: {
					element: {
						selector: ".mepr_pro_error ul",
						label: FieldError.LABEL,
					},
				},
			}));

		it("can be submitted with right token", () =>
			submitForm({
				formSelector: formSelector,
				captchaValue: CaptchaValue.RIGHT,
				expectedResult: successfulSubmissionResult,
			}));
	});
});
