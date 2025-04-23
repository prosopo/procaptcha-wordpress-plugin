import { FormSubmitionSettings, submitForm } from "./commands/submit-form";
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

			submitForm(settings: FormSubmitionSettings): Chainable<JQuery>;

			removePosts(settings: RemovePostSettings): Chainable<JQuery>;

			removeUsers(settings: RemoveUsersSettings): Chainable<JQuery>;

			getForm(formElementSelector: string): Chainable<JQuery>;

			assertProcaptchaExistence(
				shouldExist: boolean,
				selectorOrElement: string | JQuery,
			): Chainable<JQuery>;

			togglePlugins(
				enable: boolean,
				pluginSlugs: string[],
			): Chainable<string[]>;
		}
	}
}

Cypress.Commands.add(
	"togglePlugins",
	(enable: boolean, pluginSlugs: string[]) => {
		cy.login();

		const affectedPluginSlugs: string[] = [];
		const action = enable ? "activate" : "deactivate";

		return cy
			.wrap(pluginSlugs)
			.each((slug: string) => {
				let selector = "#" + action + "-" + slug;

				cy.visit("/wp-admin/plugins.php");

				cy.get("body").then(($body) => {
					// optional, as the plugin may be already active (avoid breaks if tests are run locally).
					if (0 === $body.find(selector).length) {
						return;
					}

					// visit instead of the click, as some plugins have deactivation survey popups.
					cy.get(selector)
						.invoke("attr", "href")
						.then((href) => {
							cy.visit("/wp-admin/" + href);
						});

					// check for url instead of the notice, as some plugins (like BBPress) make a redirect.
					cy.url().should("not.equal", "/wp-admin/plugins.php");

					affectedPluginSlugs.push(slug);
				});
			})
			.then(() => affectedPluginSlugs);
	},
);

Cypress.Commands.add(
	"assertProcaptchaExistence",
	(shouldExist: boolean, selectorOrElement: string | JQuery): void => {
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
	},
);

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

Cypress.Commands.add("submitForm", (settings: FormSubmitionSettings): void => {
	submitForm(settings);
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
