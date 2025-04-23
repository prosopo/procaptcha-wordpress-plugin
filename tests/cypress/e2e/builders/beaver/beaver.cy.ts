import { IntegrationTest } from "@support/integration-test";
import { BeaverLogin } from "./classes/beaver-login";

new IntegrationTest({
	targetPluginSlugs: ["bb-plugin"],
	forms: [new BeaverLogin()],
});
