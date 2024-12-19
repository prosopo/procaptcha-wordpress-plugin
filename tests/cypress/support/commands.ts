import {
	Settings as SubmitFormSettings,
	SubmitForm,
} from "./commands/submit-form";
import Login from "./commands/login";
import SafeType from "./commands/safe-type";
import {
	RemovePosts,
	Settings as RemovePostSettings,
} from "./commands/remove-posts";
import {
	RemoveUsers,
	Settings as RemoveUsersSettings,
} from "./commands/remove-users";
import Chainable = Cypress.Chainable;

declare global {
	namespace Cypress {
		interface Chainable {
			safeType(
				selectorOrElement: string | JQuery,
				value: string,
			): Chainable<JQuery>;

			login(url?: string): Chainable<JQuery>;

			submitForm(settings: SubmitFormSettings): Chainable<JQuery>;

			removePosts(settings: RemovePostSettings): Chainable<JQuery>;

			removeUsers(settings: RemoveUsersSettings): Chainable<JQuery>;

			getForm(formElementSelector: string): Chainable<JQuery>;
		}
	}
}

// with workarounds, otherwise .type below writes too fast, or just fails.
Cypress.Commands.add(
	"safeType",
	(selectorOrElement: string | JQuery, value: string): void => {
		new SafeType(selectorOrElement, value);
	},
);

Cypress.Commands.add("login", (url: string = "/wp-login.php"): void => {
	// save cookies, to avoid logging in multiple times.
	cy.session(
		["procaptcha", "procaptcha"],
		() => {
			// Make sure nothing exist yet (to avoid GitHub Actions inner cache-related issues).
			cy.clearCookies();
			cy.clearLocalStorage();

			new Login(url);
		},
		{
			validate: () => {
				cy.visit("/wp-admin/");

				cy.get("h1").should("include.text", "Dashboard");
			},
		},
	);
});

Cypress.Commands.add("submitForm", (settings: SubmitFormSettings): void => {
	new SubmitForm(settings);
});

Cypress.Commands.add("removePosts", (settings: RemovePostSettings): void => {
	new RemovePosts(settings);
});

Cypress.Commands.add("removeUsers", (settings: RemoveUsersSettings): void => {
	new RemoveUsers(settings);
});

Cypress.Commands.add(
	"getForm",
	(formElementSelector: string): Chainable<JQuery> => {
		return cy.get(formElementSelector).then(($formElement) => {
			if ("form" === $formElement.prop("tagName").toLowerCase()) {
				return cy.wrap($formElement);
			}

			let $form = $formElement.find("form");

			if ($form.length > 0) {
				return cy.wrap($form);
			}

			return cy.wrap($formElement);
		});
	},
);
