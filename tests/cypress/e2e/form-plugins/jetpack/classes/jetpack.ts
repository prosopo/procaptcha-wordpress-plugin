import { FormTest } from "@support/form-test";

class Jetpack extends FormTest {
	protected defineSettings() {
		super.defineSettings();

		// fixme it doesn't work properly when 2 forms on the same page
		this.url = "/jetpack/";
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

	protected afterScenario() {
		super.afterScenario();

		it("removeResponseRecords", () => {
			cy.removePosts({
				postType: "feedback",
				countToRemove: 4,
				onlyIfTotal: 4,
			});
		});
	}
}

export default Jetpack;
