export const activatePluginsForTestLifetime = (
	pluginSlugsToActivate: string[],
) => {
	let pluginSlugsToDeactivate: string[] = [];

	before(() => {
		togglePlugins("activate", pluginSlugsToActivate).then(
			(disabledPluginSlugs) => {
				pluginSlugsToDeactivate = disabledPluginSlugs;
			},
		);
	});

	after(() => {
		if (pluginSlugsToDeactivate) {
			togglePlugins("deactivate", pluginSlugsToDeactivate);
		}
	});
};

const togglePlugins = (action: string, pluginSlugs: string[]) => {
	cy.login();

	const affectedPluginSlugs: string[] = [];

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
};
