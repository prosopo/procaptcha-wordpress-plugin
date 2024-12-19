import { IntegrationTest } from "@support/integration-test";
import FluentForms from "./classes/fluent-forms";

new IntegrationTest({
	targetPluginSlugs: ["fluentform"],
	forms: [new FluentForms()],
});
