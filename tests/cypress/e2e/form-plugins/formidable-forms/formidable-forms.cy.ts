import { IntegrationTest } from "@support/integration-test";
import FormidableForms from "./classes/formidable-forms";
import FormidableFormsWithoutAjax from "./classes/formidable-forms-without-ajax";

new IntegrationTest({
	targetPluginSlugs: ["formidable"],
	forms: [new FormidableForms(), new FormidableFormsWithoutAjax()],
});
