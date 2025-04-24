interface FormSubmitionSettings {
	captchaValue?: string;
	fieldValues?: object;
	formSelector?: string;
	captchaInputSelector?: string;
	submitButtonSelector?: string;
	expectedResult?: ExpectedResult;
}

interface ExpectedResult {
	element?: {
		selector: string;
		label: string;
	};
}

const submitForm = (options: FormSubmitionSettings): void => {
	const settings = {
		...{
			captchaValue: "",
			fieldValues: {},
			formSelector: "form",
			captchaInputSelector: "",
			submitButtonSelector: "[type=submit], button",
		},
		...options,
	};

	cy.getForm(settings.formSelector).then(($form) => {
		fillRequiredInputs($form);

		if (settings.captchaValue.length > 0) {
			setCaptchaValue($form, settings);
		}

		populateFieldValues(settings);

		cy.wrap($form)
			.find(settings.submitButtonSelector)
			.then(($buttons) => {
				const $submitButtons = $buttons.filter("[type=submit]");

				const $submitButton =
					$submitButtons.length > 0
						? $submitButtons.first()
						: $buttons.first();

				cy.wrap($submitButton).click();

				if (settings.expectedResult) {
					checkExpectedResult(settings.expectedResult);
				}
			});
	});
};

const fillRequiredInputs = ($form: JQuery): void => {
	let $inputs = $form.find("input[required]:not([type=hidden])");

	if (0 === $inputs.length) {
		return;
	}

	$inputs.each((index, input) => {
		cy.safeType(Cypress.$(input), "procaptcha");
	});
};

const setCaptchaValue = (
	$form: JQuery,
	settings: FormSubmitionSettings,
): void => {
	if (settings.captchaInputSelector.length > 0) {
		cy.log(
			`setting captcha value as ${settings.captchaValue} to ${settings.captchaInputSelector}`,
		);

		cy.wrap($form)
			.find(settings.captchaInputSelector)
			.invoke("val", settings.captchaValue);

		return;
	}

	cy.log(`settings captcha value as ${settings.captchaValue} to [new input]`);

	const inputName = "procaptcha-response";

	cy.wrap($form)
		.invoke(
			"append",
			`<input type="hidden" name="${inputName}" value="${settings.captchaValue}">`,
		)
		.then(($form) => {
			$form[0].querySelector(`input[name=${inputName}]`).dispatchEvent(
				new CustomEvent("_prosopo-procaptcha__filled", {
					detail: { token: settings.captchaValue },
				}),
			);
		});
};

const populateFieldValues = (settings: FormSubmitionSettings): void => {
	for (let fieldName in settings.fieldValues) {
		let isFieldSelector =
			-1 !== fieldName.indexOf(".") ||
			-1 !== fieldName.indexOf("#") ||
			-1 !== fieldName.indexOf("[");

		let selector =
			false === isFieldSelector
				? settings.formSelector +
					' input[name="' +
					fieldName +
					'"],' +
					settings.formSelector +
					' textarea[name="' +
					fieldName +
					'"],' +
					settings.formSelector +
					' select[name="' +
					fieldName +
					'"]'
				: settings.formSelector + " " + fieldName;

		cy.safeType(selector, settings.fieldValues[fieldName]);
	}
};

const checkExpectedResult = (expectedResult: ExpectedResult): void => {
	if (expectedResult.element) {
		cy.get(expectedResult.element.selector)
			.should("be.visible")
			.should("include.text", expectedResult.element.label);
	}
};

export { submitForm, FormSubmitionSettings, ExpectedResult };
