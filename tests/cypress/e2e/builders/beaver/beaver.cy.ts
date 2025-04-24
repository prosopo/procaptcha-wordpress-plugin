import { IntegrationTest } from "@support/integration-test";
import { BeaverLoginForm } from "./beaver-login-form";

new IntegrationTest({
	targetPluginSlugs: ["beaver-builder-plugin-starter-version"],
	forms: [new BeaverLoginForm()],
});
