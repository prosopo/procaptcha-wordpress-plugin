import { LoginForm } from "@wordpress/login-form";
import { LostPasswordForm } from "@wordpress/lost-password-form";
import { IntegrationTest } from "@support/integration-test";
import { RegisterForm } from "./classes/register-form";

new IntegrationTest({
	targetPluginSlugs: ["better-wp-security"],
	loginUrl: "/wp-login.php?itsec-hb-token=wplogin",
	forms: [
		new LoginForm({
			url: "/wp-login.php?itsec-hb-token=wplogin",
		}),
		new RegisterForm(),
		new LostPasswordForm({
			url: "/wp-login.php?action=lostpassword&itsec-hb-token=wplogin",
		}),
	],
});
