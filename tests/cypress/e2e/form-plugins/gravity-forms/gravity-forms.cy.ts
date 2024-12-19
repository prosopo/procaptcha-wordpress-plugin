import { IntegrationTest } from "@support/integration-test";
import GravityForms from "./classes/gravity-forms";
import GravityFormsWithAjax from "./classes/gravity-forms-with-ajax";

new IntegrationTest({
	targetPluginSlugs: [
		// it's the slug according to the plugin folder (during activation).
		"gravity-forms",
		// it's the slug according to the plugin setup (during deactivation).
		"gravityforms",
	],
	forms: [new GravityForms(), new GravityFormsWithAjax()],
});
