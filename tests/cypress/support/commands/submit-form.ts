interface Settings {
	captchaValue?: string;
	fieldValues?: object;
	formSelector?: string;
	captchaInputSelector?: string;
	submitButtonSelector?: string;
}

class SubmitForm {
	private settings: Settings;

	constructor(settings: Settings) {
		this.settings = Object.assign(
			{
				captchaValue: "",
				fieldValues: {},
				formSelector: "form",
				captchaInputSelector: "",
				submitButtonSelector: "[type=submit], button",
			},
			settings,
		);

		cy.getForm(this.settings.formSelector).then(($form) => {
			this.submit($form);
		});
	}

	protected fillRequiredInputs($form: JQuery): void {
		let $inputs = $form.find("input[required]:not([type=hidden])");

		if (0 === $inputs.length) {
			return;
		}

		$inputs.each((index, input) => {
			cy.safeType(Cypress.$(input), "procaptcha");
		});
	}

	protected setCaptchaValue($form: JQuery): void {
		if ("" === this.settings.captchaValue) {
			return;
		}

		if ("" === this.settings.captchaInputSelector) {
			cy.wrap($form)
				.invoke(
					"append",
					'<input type="hidden" name="procaptcha-response" value="' +
						this.settings.captchaValue +
						'">',
				)
				.then(($form) => {
					$form[0]
						.querySelector("input[name=procaptcha-response]")
						.dispatchEvent(
							new CustomEvent("_prosopo-procaptcha__filled", {
								detail: { token: this.settings.captchaValue },
							}),
						);
					cy.log(
						'even is fired: "' + this.settings.captchaValue + '"',
					); //fixme
				});

			return;
		}

		cy.wrap($form)
			.find(this.settings.captchaInputSelector)
			.invoke("val", this.settings.captchaValue);
	}

	protected populateFieldValues(): void {
		for (let fieldName in this.settings.fieldValues) {
			let isFieldSelector =
				-1 !== fieldName.indexOf(".") ||
				-1 !== fieldName.indexOf("#") ||
				-1 !== fieldName.indexOf("[");

			let selector =
				false === isFieldSelector
					? this.settings.formSelector +
						' input[name="' +
						fieldName +
						'"],' +
						this.settings.formSelector +
						' textarea[name="' +
						fieldName +
						'"],' +
						this.settings.formSelector +
						' select[name="' +
						fieldName +
						'"]'
					: this.settings.formSelector + " " + fieldName;

			cy.safeType(selector, this.settings.fieldValues[fieldName]);
		}
	}

	protected submit($form: JQuery): void {
		this.fillRequiredInputs($form);
		this.setCaptchaValue($form);
		this.populateFieldValues();

		cy.wrap($form)
			.find(this.settings.submitButtonSelector)
			.then(($buttons) => {
				const $submitButtons = $buttons.filter("[type=submit]");

				const $submitButton =
					$submitButtons.length > 0
						? $submitButtons.first()
						: $buttons.first();

				cy.wrap($submitButton).click();
			});
	}
}

export { SubmitForm, Settings };
