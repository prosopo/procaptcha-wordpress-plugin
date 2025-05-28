import type {
	ExpectedResult,
	FormSubmitionSettings,
} from "@support/commands/submitForm";
import { activatePluginsForTestLifetime } from "@support/pluginsManagement";
import { toggleOption } from "@support/options";
import { CaptchaValue, FieldError } from "@support/form-test";

let submissionsCount = 0;

const submitForm = (settings: FormSubmitionSettings) =>
	cy.submitForm({
		fieldValues: {
			user_first_name: "test",
			user_last_name: "test",
			user_login: "test",
			user_email: "test@gmail.com",
			mepr_user_password: "test",
			mepr_user_password_confirm: "test",
		},
		valuePrefix: (++submissionsCount).toString(),
		submitButtonSelector: ".mepr-submit",
		...settings,
	});

const successfulSubmissionResult = {
	element: {
		selector: ".thankyou h2",
		label: "Thank you for your purchase",
	},
} as ExpectedResult;

const formSelector = "#mepr_signup_form";

activatePluginsForTestLifetime(["memberpress"]);

describe("register form", () => {
	beforeEach(() => cy.visit("/register/procaptcha/"));

	after(() => {
		cy.removeUsers({
			countToRemove: 2,
		});
	});

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
			toggleOption({
				pageUrl: "/wp-admin/post.php?post=1670&action=edit",
				inputSelector: "#prosopo_procaptcha",
				inputValue: isEnabled,
				submitSelector: "#publish",
			});

		before(() => {
			toggleProtection(true);
		});

		after(() => {
			toggleProtection(false);
		});

		it("has captcha", () =>
			cy.assertProcaptchaExistence(true, formSelector));

		it("can not be submitted with wrong token", () =>
			submitForm({
				formSelector: formSelector,
				captchaValue: CaptchaValue.WRONG,
				expectedResult: {
					element: {
						selector: ".mepr_error ul",
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
