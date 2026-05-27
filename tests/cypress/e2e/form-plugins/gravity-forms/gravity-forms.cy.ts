import { IntegrationTest } from "@support/integration-test";
import GravityForms from "./classes/gravity-forms";

// GravityFormsWithAjax is temporarily excluded: its beforeScenario edits a post
// via the Gutenberg classic-shortcode block, but newer WordPress/Gutenberg
// throws "TypeError: Cannot destructure property 'documentElement' of 'z'" on
// the edit screen and leaves the canvas empty. Re-enable once the editor
// interaction is replaced with a REST/WP-CLI-based shortcode update.
new IntegrationTest({
	targetPluginSlugs: [
		// it's the slug according to the plugin folder (during activation).
		"gravity-forms",
		// it's the slug according to the plugin setup (during deactivation).
		"gravityforms",
	],
	forms: [new GravityForms()],
});
