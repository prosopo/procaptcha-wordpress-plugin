import { LoginForm } from "@wordpress/login-form";
import { RegisterForm } from "@wordpress/register-form";
import { LostPasswordForm } from "@wordpress/lost-password-form";
import { IntegrationTest } from "@support/integration-test";

new IntegrationTest({
	targetPluginSlugs: ["ultimate-dashboard"],
	loginUrl: "/login",
	forms: [
		new LoginForm({
			url: "/login",
		}),
		new RegisterForm({
			url: "/login/?action=register",
		}),
		new LostPasswordForm({
			url: "/login/?action=lostpassword",
		}),
	],
});
