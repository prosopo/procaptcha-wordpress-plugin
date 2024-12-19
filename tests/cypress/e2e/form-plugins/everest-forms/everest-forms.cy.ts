import { IntegrationTest } from "@support/integration-test";
import EverestForms from "./classes/everest-forms";
import EverestFormsWithAjax from "./classes/everest-forms-with-ajax";

new IntegrationTest({
	targetPluginSlugs: ["everest-forms"],
	forms: [new EverestForms(), new EverestFormsWithAjax()],
});
