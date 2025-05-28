export const toggleProcaptchaOption = (
	tab: string,
	settingName: string,
	isActive: boolean,
) =>
	toggleOption({
		pageUrl:
			"/wp-admin/options-general.php?page=prosopo-procaptcha&tab=" + tab,
		inputSelector: `input[name="${settingName}"]`,
		inputValue: isActive,
		submitSelector: 'input[name="prosopo-captcha__submit"]',
	});

interface ToggleSettings {
	pageUrl: string;
	inputSelector: string;
	inputValue: unknown;
	submitSelector: string;
}

export const toggleOption = (settings: ToggleSettings) => {
	cy.login();

	cy.visit(settings.pageUrl);

	let input = cy.get(settings.inputSelector, {
		includeShadowDom: true,
	});

	// force, as it can be hidden (opacity:0).
	switch (settings.inputValue) {
		case true:
			input.check({ force: true });
			break;
		case false:
			input.uncheck({ force: true });
			break;
	}

	cy.get(settings.submitSelector, {
		includeShadowDom: true,
	}).click();

	// avoid affecting the first coming test.
	cy.clearAllCookies();
};
