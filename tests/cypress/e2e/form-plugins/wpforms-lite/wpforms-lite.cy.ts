import { IntegrationTest } from "@support/integration-test";
import WpFormsWithAjax from "./classes/wp-forms-with-ajax";
import WpForms from "./classes/wp-forms";

new IntegrationTest({
	targetPluginSlugs: ["wpforms-lite"],
	forms: [new WpForms(), new WpFormsWithAjax()],
});
