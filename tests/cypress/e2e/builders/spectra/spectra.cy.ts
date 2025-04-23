import { IntegrationTest } from "@support/integration-test";
import { SpectraFormBlock } from "./classes/spectra-form-block";

new IntegrationTest({
	targetPluginSlugs: ["ultimate-addons-for-gutenberg"],
	forms: [new SpectraFormBlock()],
});
