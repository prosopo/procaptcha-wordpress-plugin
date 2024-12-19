import { IntegrationTest } from "@support/integration-test";
import ElementorLogin from "./classes/elementor-login";
import ElementorForms from "./classes/elementor-forms";

new IntegrationTest({
	targetPluginSlugs: ["elementor", "elementor-pro"],
	forms: [new ElementorForms(), new ElementorLogin()],
});
