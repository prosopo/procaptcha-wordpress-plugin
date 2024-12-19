import { IntegrationTest } from "@support/integration-test";
import ContactForm7 from "./classes/contact-form-7";

new IntegrationTest({
	targetPluginSlugs: ["contact-form-7"],
	forms: [new ContactForm7()],
});
