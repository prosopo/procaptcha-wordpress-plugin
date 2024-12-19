import { IntegrationTest } from "@support/integration-test";
import NinjaForms from "./classes/ninja-forms";

new IntegrationTest({
	targetPluginSlugs: ["ninja-forms"],
	forms: [new NinjaForms()],
});
