export const setProcaptchaOption = (
	tab: string,
	settingName: string,
	value: boolean,
) => {
	cy.login();

	cy.visit(
		"/wp-admin/options-general.php?page=prosopo-procaptcha&tab=" + tab,
	);

	let input = cy.get(`input[name="${settingName}"]`, {
		includeShadowDom: true,
	});

	// force, as they're hidden (opacity:0).
	true === value
		? input.check({ force: true })
		: input.uncheck({ force: true });

	cy.get('input[name="prosopo-captcha__submit"]', {
		includeShadowDom: true,
	}).click();

	// avoid affecting the first coming test.
	cy.clearAllCookies();
};
