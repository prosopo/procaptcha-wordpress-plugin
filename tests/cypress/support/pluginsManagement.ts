export const activatePluginsForTestLifetime = (
	pluginSlugsToActivate: string[],
) => {
	let temporaryEnabledPluginSlugs: string[] = [];

	before(() => {
		cy.login();

		togglePlugins("activate", pluginSlugsToActivate).then(
			(enabledPluginSlugs) => {
				temporaryEnabledPluginSlugs = enabledPluginSlugs;

				cy.log(
					`temporary activated plugins: ${temporaryEnabledPluginSlugs.join(",")}`,
				);

				// avoid affecting the first coming test.
				cy.clearAllCookies();
			},
		);
	});

	after(() => {
		if (temporaryEnabledPluginSlugs) {
			cy.login();

			togglePlugins("deactivate", temporaryEnabledPluginSlugs).then(
				() => {
					cy.log(
						`deactivated temporary active plugins: ${temporaryEnabledPluginSlugs.join(",")}`,
					);

					// avoid affecting others.
					cy.clearAllCookies();
				},
			);
		}
	});
};

const togglePlugins = (action: string, pluginSlugs: string[]) => {
	const affectedPluginSlugs: string[] = [];

	cy.visit("/wp-admin/plugins.php");

	cy.get("#the-list tr[data-slug]").then(($elements) => {
		const slugs = Array.from($elements).map((element) =>
			element.getAttribute("data-slug"),
		);

		cy.log("on-page plugin slugs: ", slugs.join(", "));
	});

	return cy
		.wrap(pluginSlugs)
		.each((pluginSlug: string) => {
			cy.visit("/wp-admin/plugins.php");

			cy.get("body").then(($body) => {
				let selector = `#${action}-${pluginSlug}, 
				#the-list tr[data-slug="${pluginSlug}"] .${action} a`;

				// optional, as the plugin may be already active (avoid breaks if tests are run locally).
				if (0 === $body.find(selector).length) {
					cy.log(
						`skipping plugin toggling: ${pluginSlug}, selector was: ${selector}`,
					);

					return;
				}

				// visit instead of the click, as some plugins have deactivation survey popups.
				cy.get(selector)
					.first()
					.invoke("attr", "href")
					.then((href) => {
						cy.visit("/wp-admin/" + href);
					});

				// check for url instead of the notice, as some plugins (like BBPress) make a redirect.
				cy.url().should("not.equal", "/wp-admin/plugins.php");

				affectedPluginSlugs.push(pluginSlug);
			});
		})
		.then(() => affectedPluginSlugs);
};
