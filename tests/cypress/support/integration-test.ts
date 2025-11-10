import {FormTest} from "./form-test";

enum PluginAction {
    ACTIVATE = "activate",
    DEACTIVATE = "deactivate",
}

interface Settings {
    forms: FormTest[];
    loginUrl?: string;
    targetPluginSlugs?: string[];
}

/**
 * @deprecated Use composition instead inheritance (see jetpack for example)
 */
export class IntegrationTest {
    private pluginSlugsToDeactivate: string[];
    private targetPluginSlugs: string[];
    private loginUrl: string;
    private forms: FormTest[];

    constructor(settings: Settings) {
        this.pluginSlugsToDeactivate = [];
        this.targetPluginSlugs = settings.targetPluginSlugs || [];
        this.loginUrl = settings.loginUrl || "/wp-login.php";
        this.forms = settings.forms;

        this.test();
    }

    protected toggleTargetPlugins(action: string, loginUrl: string): void {
        // deactivate only plugins that were inactive (avoid breaks if the tests are run locally).
        let targetPlugins =
            PluginAction.ACTIVATE === action
                ? this.targetPluginSlugs
                : this.pluginSlugsToDeactivate;

        it("toggleTargetPlugins", () => {
            if (0 === targetPlugins.length) {
                return;
            }

            cy.login(loginUrl);

            targetPlugins.forEach((slug) => {
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

                    if (PluginAction.ACTIVATE === action) {
                        this.pluginSlugsToDeactivate.push(slug);
                    }
                });
            });
        });
    }

    protected before(): void {
        describe("activateTargetPlugins", () => {
            // Url is hardcoded, as before the test the url is stock,
            // even if we're going to activate the plugin that changes it.
            this.toggleTargetPlugins("activate", "/wp-login.php");
        });
    }

    protected after(): void {
        describe("deactivateTargetPlugins", () => {
            this.toggleTargetPlugins("deactivate", this.loginUrl);
        });
    }

    protected test(): void {
        this.before();

        this.forms.forEach((form) => {
            form.test(this);
        });

        this.after();
    }

    public login(loginUrl: string = ""): void {
        loginUrl = loginUrl || this.loginUrl;

        cy.login(loginUrl);
    }
}
