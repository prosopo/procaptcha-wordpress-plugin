import { LoginForm } from "@wordpress/login-form";
import { RegisterForm } from "@wordpress/register-form";
import { LostPasswordForm } from "@wordpress/lost-password-form";
import { IntegrationTest } from "@support/integration-test";

// fixme uncomment when fixed https://wordpress.org/support/topic/cant-reset-password-when-brute-force-option-rename-login-is-on/
/*   new IntegrationTest({
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
});*/
