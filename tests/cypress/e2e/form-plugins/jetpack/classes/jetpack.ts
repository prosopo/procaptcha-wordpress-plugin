import { FormTest } from "@support/form-test";

interface JetpackSettings {
	expectedSubmissionsCount: number;
}

class Jetpack extends FormTest {
	// two forms on the same page just doesn't work (even with vanilla setup)...
	constructor(private readonly settings: JetpackSettings) {
		super();
	}

	protected defineSettings() {
		super.defineSettings();

		this.isAuthSupportedByVendor = true;
		this.selectors = {
			formWithCaptcha: ".jetpack-form-with-captcha",
			formWithoutCaptcha: ".jetpack-form-without-captcha",
			successMessage: "#contact-form-success-header",
			errorMessage: ".form-error h3",
			errorFieldMessage: ".form-errors",
			captchaInput: "",
		};
		this.submitValues = {
			"input.name.grunion-field": "John Doe",
		};
		this.messages = {
			success: "Your message has been sent",
			fail: "Error!",
		};
	}

	public setUrl(url: string) {
		this.url = url;
	}

	public setFormWithCaptchaSelector(selector: string) {
		this.selectors.formWithCaptcha = selector;
	}

	public setFormWithoutCaptchaSelector(selector: string) {
		this.selectors.formWithoutCaptcha = selector;
	}

	protected afterScenario() {
		super.afterScenario();

		it("removeResponseRecords", () => {
			cy.removePosts({
				postType: "feedback",
				countToRemove: this.settings.expectedSubmissionsCount,
				onlyIfTotal: this.settings.expectedSubmissionsCount,
			});
		});
	}
}

export default Jetpack;
