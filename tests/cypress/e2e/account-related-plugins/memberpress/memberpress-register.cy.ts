import type {
	ExpectedResult,
	FormSubmitionSettings,
} from "@support/commands/submitForm";
import { LoginCredentials } from "@wordpress/login-form";
import { activatePluginsForTestLifetime } from "@support/pluginsManagement";
import { setProcaptchaOption } from "@support/procaptchaOptions";
import { CaptchaValue, FieldError } from "@support/form-test";

const submitForm = (settings: FormSubmitionSettings) =>
	cy.submitForm({
		fieldValues: {
			// fixme prefix
			user_first_name: "test",
			user_last_name: "test",
			user_login: "test",
			user_email: "test",
			mepr_user_password: "test",
			mepr_user_password_confirm: "test",
		},
		submitButtonSelector: ".mepr-submit",
		...settings,
	});

// fixme update to the register
const successfulSubmissionResult = {
	element: {
		selector: "#mepr_loginform",
		shouldBeMissing: true,
	},
} as ExpectedResult;

activatePluginsForTestLifetime(["memberpress"]);

describe("register form", () => {
	const formSelector = "#mepr_loginform";

	beforeEach(() => cy.visit("/register/procaptcha/"));

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
