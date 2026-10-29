const LOGIN_ACTION = "action=user_registration_ajax_login_submit";
const LOGIN_BUTTON_SELECTOR = "#user_registration_ajax_login_submit";
const TOKEN_FIELD_NAME = "procaptcha-response";

const getLoginForms = (): HTMLFormElement[] =>
	Array.from(document.querySelectorAll(LOGIN_BUTTON_SELECTOR))
		.map((button) => button.closest("form"))
		.filter((form): form is HTMLFormElement => null !== form);

const getFormToken = (form: HTMLFormElement): string => {
	const input = form.querySelector(`input[name="${TOKEN_FIELD_NAME}"]`);

	return input instanceof HTMLInputElement ? input.value : "";
};

const getLoginToken = (): string => {
	const forms = getLoginForms();
	// The clicked submit button is focused, so prefer its form when there are several.
	const focusedForm = forms.find((form) =>
		form.contains(document.activeElement),
	);
	const form = focusedForm || forms.find((form) => "" !== getFormToken(form));

	return undefined !== form ? getFormToken(form) : "";
};

/**
 * With the "AJAX login" option on, User Registration posts only its own fields,
 * so the token must be added for the WordPress authentication check to see it.
 */
const addTokenToUserRegistrationAjaxLogin = ($: JQueryStatic): void => {
	$.ajaxPrefilter((options) => {
		if (
			"string" !== typeof options.url ||
			false === options.url.includes(LOGIN_ACTION) ||
			"string" !== typeof options.data
		) {
			return;
		}

		const fields = new URLSearchParams(options.data);

		if (false === fields.has(TOKEN_FIELD_NAME)) {
			fields.set(TOKEN_FIELD_NAME, getLoginToken());
			options.data = fields.toString();
		}
	});
};

export { addTokenToUserRegistrationAjaxLogin };
