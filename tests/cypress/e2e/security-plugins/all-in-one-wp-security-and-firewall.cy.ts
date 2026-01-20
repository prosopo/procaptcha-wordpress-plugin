import {LoginForm} from "@wordpress/login-form";
import {RegisterForm} from "@wordpress/register-form";
import {LostPasswordForm} from "@wordpress/lost-password-form";
import {IntegrationTest} from "@support/integration-test";

new IntegrationTest({
	targetPluginSlugs: ["all-in-one-wp-security-and-firewall"],
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
