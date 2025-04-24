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

	return cy
		.wrap(pluginSlugs)
		.each((pluginSlug: string) => {
			let selector = "#" + action + "-" + pluginSlug;

			cy.visit("/wp-admin/plugins.php");

			cy.get("body").then(($body) => {
				// optional, as the plugin may be already active (avoid breaks if tests are run locally).
				if (0 === $body.find(selector).length) {
					cy.log(`skipping plugin toggling: ${pluginSlug}`);

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

				affectedPluginSlugs.push(pluginSlug);
			});
		})
		.then(() => affectedPluginSlugs);
};
