import type {
	ExpectedResult,
	FormSubmitionSettings,
} from "@support/commands/submitForm";
import { CaptchaValue, FieldError } from "@support/form-test";
import { activatePluginsForTestLifetime } from "@support/pluginsManagement";
import { LoginCredentials } from "@wordpress/login-form";
import { setProcaptchaOption } from "@support/procaptchaOptions";

const submitForm = (settings: FormSubmitionSettings) =>
	cy.submitForm({
		fieldValues: {
			mepr_user_or_email: LoginCredentials.USERNAME,
		},
		submitButtonSelector: "#wp-submit",
		...settings,
	});

const successfulSubmissionResult = {
	element: {
		selector: ".mepr_password_reset_requested",
		label: "Successfully requested password reset",
	},
} as ExpectedResult;

activatePluginsForTestLifetime(["memberpress"]);

describe("reset password form", () => {
	const formSelector = "#mepr_forgot_password_form";

	beforeEach(() => cy.visit("/login/?action=forgot_password"));

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
			setProcaptchaOption(
				"account-forms",
				"is_on_wp_lost_password_form",
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
