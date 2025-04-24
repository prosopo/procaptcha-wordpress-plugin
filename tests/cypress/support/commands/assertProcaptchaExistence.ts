import Chainable = Cypress.Chainable;

export const assertProcaptchaExistence = (
	shouldExist: boolean,
	selectorOrElement: string | JQuery,
): void => {
	const element: Chainable<JQuery> =
		"string" === typeof selectorOrElement
			? cy.get(<string>selectorOrElement)
			: cy.wrap(<JQuery>selectorOrElement);

	const assertion = shouldExist ? "exist" : "not.exist";

	element.find(".prosopo-procaptcha-wp-widget").should(assertion);

	// check only in the 'exist' case, as for "not.exist" case,
	// the script could have been added for the other form.
	if (shouldExist) {
		cy.get("script#prosopo-procaptcha-js").should("exist");
	}
};
