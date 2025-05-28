import { IntegrationTest } from "./integration-test";
import { FormSubmitionSettings } from "@support/commands/submitForm";
import AUTWindow = Cypress.AUTWindow;

interface Selectors {
	formWithCaptcha: string;
	formWithoutCaptcha: string;
	successMessage: string;
	errorMessage: string;
	errorFieldMessage: string;
	captchaInput: string;
	submitButton?: string;
}

interface Messages {
	success: string;
	fail: string;
	failOnMissingCaptcha?: string;
	fieldFail?: string;
}

enum Role {
	GUEST = "guest",
	USER = "user",
}

enum FormSelector {
	CUSTOM_CAPTCHA_INPUT = ".prosopo-procaptcha-input",
}

enum Message {
	VALIDATION_ERROR = "Please verify that you are human.",
}

export enum CaptchaValue {
	WRONG = "wrong",
	RIGHT = "bypass",
}

export enum FieldError {
	SELECTOR = ".prosopo-procaptcha-wp-form__error",
	LABEL = "Please verify that you are human.",
}

abstract class FormTest {
	private submitCounter: number;

	protected integrationTest: IntegrationTest;
	protected url: string;
	protected isAuthSupportedByVendor: boolean;
	protected isClientSideFieldValidationSupported: boolean;
	protected selectors: Selectors;
	protected submitValues: object;
	protected messages: Messages;

	constructor() {
		this.submitCounter = 0;

		this.url = "";
		this.isAuthSupportedByVendor = true;
		this.isClientSideFieldValidationSupported = true;
		this.selectors = {
			formWithCaptcha: "",
			formWithoutCaptcha: "",
			successMessage: "",
			errorMessage: "",
			errorFieldMessage: "",
			captchaInput: "",
			submitButton: "",
		};
		this.submitValues = {};
		this.messages = {
			success: "",
			fail: "",
		};

		this.defineSettings();
	}

	protected defineSettings(): void {}

	// field error can be either plugin-specific or generic (ours).
	protected checkFieldError(
		formSelector: string,
		errorFieldMessageSelector: string,
	): void {
		cy.get(formSelector).then(($form) => {
			let $builtInError = $form.find(FieldError.SELECTOR);

			if (
				true === $builtInError.is(":visible") &&
				true === $builtInError.text().includes(Message.VALIDATION_ERROR)
			) {
				return;
			}

			let failMessage =
				this.messages.fieldFail || Message.VALIDATION_ERROR;

			cy.wrap($form)
				// check the serverError exactly using .find(), as it gives time for JS-based forms to update the DOM.
				.find(errorFieldMessageSelector)
				.should("be.visible")
				.should("include.text", failMessage);
		});
	}

	protected getSuccessfulSubmitMessageSelector(
		formSelector: string,
		userRole: string,
	): string {
		return formSelector + " " + this.selectors.successMessage;
	}

	protected getFailSubmitMessageSelector(): string {
		return (
			this.selectors.formWithCaptcha + " " + this.selectors.errorMessage
		);
	}

	// Check success: either message or no form presence.
	protected checkSuccessfulSubmit(
		formSelector: string,
		userRole: string,
	): void {
		if ("" !== this.messages.success) {
			cy.get(
				this.getSuccessfulSubmitMessageSelector(formSelector, userRole),
			)
				.should("be.visible")
				.should("include.text", this.messages.success);

			return;
		}

		// No form presence after redirect (e.g. WP forms).
		cy.get(formSelector).should("not.exist");
	}

	protected getScriptsOnThePage(window: AUTWindow): string[] {
		const scripts = window.document.querySelectorAll("script");

		return Array.from(scripts)
			.filter((script) => script.src)
			.map((script) => script.src);
	}

	// so we can see details in the console.
	protected enableDebugModeForPluginScripts(): void {
		cy.window().then((window) => {
			window.localStorage.setItem("_wp_procaptcha_debug_mode", "1");

			cy.log("Enabled debug mode for the plugin scripts.");
		});
	}

	protected printScriptsOnThePage(): void {
		cy.window().then((window) => {
			const scriptsList = this.getScriptsOnThePage(window);

			cy.log("Scripts on the page:");

			scriptsList.forEach((script) => {
				cy.log(script);
			});
		});
	}

	protected visitTargetPage(): void {
		this.enableDebugModeForPluginScripts();

		cy.visit(this.url);

		// this.printScriptsOnThePage();
	}

	protected prefixSubmitValue(key: string, value: string): string {
		return this.submitCounter.toString() + value;
	}

	protected prefixSubmitValues(submitValues: object): object {
		this.submitCounter++;

		// Make sure it values are unique, so it passes e.g. the registration form during multiple submits.
		for (let key in submitValues) {
			submitValues[key] = this.prefixSubmitValue(key, submitValues[key]);
		}

		return submitValues;
	}

	protected getSubmitValues(userRole: string): object {
		// Make clone to make sure we don't modify the original object down the line.
		return this.prefixSubmitValues({ ...this.submitValues });
	}

	protected checkCaptchaPresence(): void {
		cy.get(
			this.selectors.formWithCaptcha + " .prosopo-procaptcha-wp-widget",
		).should("exist");

		cy.get("script#prosopo-procaptcha-js").should("exist");
	}

	protected submitForm(settings: FormSubmitionSettings): void {
		if ("string" === typeof this.selectors.submitButton) {
			settings.submitButtonSelector = this.selectors.submitButton;
		}

		cy.submitForm(settings);
	}

	protected checkServerSideValidation(
		captchaValue: string,
		role: string,
	): void {
		this.submitForm({
			captchaValue: captchaValue,
			fieldValues: this.getSubmitValues(role),
			formSelector: this.selectors.formWithCaptcha,
			captchaInputSelector: this.selectors.captchaInput,
		});

		let failMessage =
			"" !== captchaValue
				? this.messages.fail
				: this.messages.failOnMissingCaptcha || this.messages.fail;

		// Check the general error (if present).
		if ("" !== failMessage) {
			let errorSelector = this.getFailSubmitMessageSelector();

			cy.get(errorSelector).should("include.text", failMessage);

			return;
		}

		// Some forms show only the field error (like WP login form).
		if ("" !== this.selectors.errorFieldMessage) {
			this.checkFieldError(
				this.selectors.formWithCaptcha,
				this.selectors.errorFieldMessage,
			);

			return;
		}

		// Fail if we have no way to check the error.
		cy.wrap(false).should("be.true");
	}

	////

	protected testGuestRoleFormWithoutCaptchaHasNoCaptcha(): void {
		it("guestRoleFormWithoutCaptchaHasNoCaptcha", () => {
			this.visitTargetPage();

			cy.get(
				this.selectors.formWithoutCaptcha + " .prosopo-procaptcha",
			).should("not.exist");
		});
	}

	protected testGuestRoleFormWithoutCaptchaCanBeSubmitted(): void {
		it("guestRoleFormWithoutCaptchaCanBeSubmitted", () => {
			this.visitTargetPage();

			this.submitForm({
				fieldValues: this.getSubmitValues(Role.GUEST),
				formSelector: this.selectors.formWithoutCaptcha,
			});

			this.checkSuccessfulSubmit(
				this.selectors.formWithoutCaptcha,
				Role.GUEST,
			);
		});
	}

	protected testUserRoleFormWithSkippedCaptchaHasNoCaptcha(): void {
		it("testUserRoleFormWithSkippedCaptchaHasNoCaptcha", () => {
			this.integrationTest.login();

			this.visitTargetPage();

			cy.get(
				this.selectors.formWithCaptcha + " .prosopo-procaptcha",
			).should("not.exist");
		});
	}

	protected testUserRoleFormWithSkippedCaptchaCanBeSubmitted(): void {
		it("testUserRoleFormWithSkippedCaptchaCanBeSubmitted", () => {
			this.integrationTest.login();

			this.visitTargetPage();

			this.submitForm({
				fieldValues: this.getSubmitValues(Role.USER),
				formSelector: this.selectors.formWithCaptcha,
			});

			this.checkSuccessfulSubmit(
				this.selectors.formWithCaptcha,
				Role.USER,
			);
		});
	}

	protected testFormWithCaptchaHasCaptcha(role: string): void {
		it("formWithCaptchaHasCaptcha", () => {
			if (Role.USER === role) {
				this.integrationTest.login();
			}

			this.visitTargetPage();

			this.checkCaptchaPresence();
		});
	}

	protected testFormWithCaptchaHasClientSideFieldValidation(
		role: string,
	): void {
		it("testFormWithCaptchaHasClientSideFieldValidation", () => {
			if (Role.USER === role) {
				this.integrationTest.login();
			}

			this.visitTargetPage();

			this.submitForm({
				fieldValues: this.getSubmitValues(role),
				formSelector: this.selectors.formWithCaptcha,
			});

			this.checkFieldError(
				this.selectors.formWithCaptcha,
				this.selectors.errorFieldMessage,
			);
		});
	}

	protected testFormWithCaptchaHasServerSideValidationForWrongValue(
		role: string,
	): void {
		it("testFormWithCaptchaHasServerSideValidationForWrongValue", () => {
			if (Role.USER === role) {
				this.integrationTest.login();
			}

			this.visitTargetPage();

			this.checkServerSideValidation("test", role);
		});
	}

	protected testFormWithCaptchaHasServerSideValidationForMissingValue(
		role: string,
	): void {
		it("testFormWithCaptchaHasServerSideValidationForMissingValue", () => {
			if (Role.USER === role) {
				this.integrationTest.login();
			}

			this.visitTargetPage();

			let form = cy.getForm(this.selectors.formWithCaptcha);

			// Bypass the native client-side validation.
			form.invoke("addClass", "prosopo-procaptcha__no-client-validation");

			if ("" !== this.selectors.captchaInput) {
				cy.get(
					this.selectors.formWithCaptcha +
						" " +
						this.selectors.captchaInput,
				).invoke("remove");
			}

			this.checkServerSideValidation("", role);
		});
	}

	protected testFormWithCaptchaCanBeSubmitted(role: string): void {
		it("formWithCaptchaCanBeSubmitted", () => {
			if (Role.USER === role) {
				this.integrationTest.login();
			}

			this.visitTargetPage();

			this.submitForm({
				captchaValue: "bypass",
				fieldValues: this.getSubmitValues(role),
				formSelector: this.selectors.formWithCaptcha,
				captchaInputSelector: this.selectors.captchaInput,
			});

			this.checkSuccessfulSubmit(this.selectors.formWithCaptcha, role);
		});
	}

	protected toggleFeatureSupport(isActivation: boolean): void {}

	protected toggleSetting(
		tab: string,
		settingName: string,
		isActivation: boolean,
	): void {
		it("toggleSetting: " + settingName, () => {
			this.integrationTest.login();

			cy.visit(
				"/wp-admin/options-general.php?page=prosopo-procaptcha&tab=" +
					tab,
			);

			let input = cy.get(`input[name="${settingName}"]`, {
				includeShadowDom: true,
			});

			// force, as they're hidden (opacity:0).
			true === isActivation
				? input.check({ force: true })
				: input.uncheck({ force: true });

			cy.get('input[name="prosopo-captcha__submit"]', {
				includeShadowDom: true,
			}).click();
		});
	}

	protected beforeScenario(): void {}

	protected testFormWithCaptcha(role: string): void {
		this.testFormWithCaptchaHasCaptcha(role);

		if (true === this.isClientSideFieldValidationSupported) {
			this.testFormWithCaptchaHasClientSideFieldValidation(role);
		}

		this.testFormWithCaptchaHasServerSideValidationForWrongValue(role);
		this.testFormWithCaptchaHasServerSideValidationForMissingValue(role);
		this.testFormWithCaptchaCanBeSubmitted(role);
	}

	protected runScenario(): void {
		// some plugins, like JetPack, doesn't support 2 forms on the same page...

		if (this.selectors.formWithoutCaptcha) {
			// initial tests without the feature enabled (for WP forms).
			this.testGuestRoleFormWithoutCaptchaHasNoCaptcha();
			this.testGuestRoleFormWithoutCaptchaCanBeSubmitted();
		}

		if (this.selectors.formWithCaptcha) {
			this.toggleFeatureSupport(true);

			this.testFormWithCaptcha(Role.GUEST);

			if (false === this.isAuthSupportedByVendor) {
				return;
			}

			this.testUserRoleFormWithSkippedCaptchaHasNoCaptcha();
			this.testUserRoleFormWithSkippedCaptchaCanBeSubmitted();

			this.toggleSetting("general", "is_enabled_for_authorized", true);
			this.testFormWithCaptcha(Role.USER);
			this.toggleSetting("general", "is_enabled_for_authorized", false);
		}
	}

	protected afterScenario(): void {
		this.toggleFeatureSupport(false);
	}

	public test(integrationTest: IntegrationTest): void {
		this.integrationTest = integrationTest;

		describe("Form " + this.url, () => {
			this.beforeScenario();
			this.runScenario();
			this.afterScenario();
		});
	}
}

export { Role, FormTest, Message, FormSelector };
