import { LoginForm } from "@wordpress/login-form";
import { RegisterForm } from "@wordpress/register-form";
import { LostPasswordForm } from "@wordpress/lost-password-form";
import { IntegrationTest } from "@support/integration-test";

new IntegrationTest({
	targetPluginSlugs: ["sg-security"],
	loginUrl: "/login",
	forms: [
		new LoginForm({
			url: "/login",
		}),
		new RegisterForm({
			url: "/signup",
		}),
		new LostPasswordForm({
			url: "/login?action=lostpassword",
		}),
	],
});
