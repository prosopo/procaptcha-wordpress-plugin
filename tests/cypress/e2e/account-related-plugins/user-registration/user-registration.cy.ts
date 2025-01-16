import { IntegrationTest } from "@support/integration-test";
import UrLoginForm from "./classes/ur-login-form";
import UrLostPasswordForm from "./classes/ur-lost-password-form";
import UserRegistration from "./classes/user-registration";

new IntegrationTest({
	targetPluginSlugs: ["user-registration"],
	forms: [
		new UserRegistration(),
		new UrLoginForm({
			url: "/user-registration-login/",
		}),
		new UrLostPasswordForm({
			url: "/user-registration-login/lost-password/",
		}),
	],
});
