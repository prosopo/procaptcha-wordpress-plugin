import Chainable = Cypress.Chainable;

class SafeType {
	private value: string;

	constructor(selectorOrElement: string | JQuery, value: string) {
		let element: Chainable<JQuery> = null;

		if ("string" === typeof selectorOrElement) {
			element = cy.get(<string>selectorOrElement);
		} else {
			element = cy.wrap(<JQuery>selectorOrElement);
		}

		this.value = value;

		element.then(($element) => {
			this.setValue($element);
		});
	}

	// 3x attempts to type the value, as some forms (like WP login, or Woo checkout),
	// seem to have specific JS that randomly causes typing issues.
	protected type($element: JQuery, attempt: number = 1): void {
		switch (attempt) {
			case 1:
				// autocomplete often randomly causes typing issues.
				$element.attr("autocomplete", "off");
				break;
			case 4:
				// We did what we could, let the test fail...
				return;
		}

		cy.wrap($element)
			.should("be.visible")
			.focus()
			.clear()
			.type(this.value, { delay: 0 })
			.then(($element) => {
				if ($element.val() === this.value) {
					return;
				}

				this.type($element, attempt + 1);
			});
	}

	protected select($element: JQuery): void {
		cy.wrap($element)
			// force, so it works for hidden as well (e.g. select2).
			.select(this.value, { force: true });
	}

	protected setValue($element: JQuery): void {
		cy.wrap($element)
			.then(($element) => {
				if ("select" === $element.prop("tagName").toLowerCase()) {
					this.select($element);
				} else {
					this.type($element);
				}
			})
			.should("have.value", this.value);
	}
}

export default SafeType;
