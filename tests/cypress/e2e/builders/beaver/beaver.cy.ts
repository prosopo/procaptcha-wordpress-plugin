import { IntegrationTest } from "@support/integration-test";
import { BeaverLoginForm } from "./beaver-login-form";

new IntegrationTest({
	targetPluginSlugs: ["bb-plugin"],
	forms: [new BeaverLoginForm()],
});
