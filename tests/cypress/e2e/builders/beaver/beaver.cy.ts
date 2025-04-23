import { IntegrationTest } from "@support/integration-test";
import { BeaverLogin } from "./classes/beaver-login";
import { BeaverContact } from "./classes/beaver-contact";

new IntegrationTest({
	targetPluginSlugs: ["bb-plugin"],
	forms: [new BeaverContact(), new BeaverLogin()],
});
