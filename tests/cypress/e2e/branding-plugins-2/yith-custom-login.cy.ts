import { LoginForm } from "@wordpress/login-form";
import { RegisterForm } from "@wordpress/register-form";
import { LostPasswordForm } from "@wordpress/lost-password-form";
import { IntegrationTest } from "@support/integration-test";

new IntegrationTest({
	targetPluginSlugs: ["yith-custom-login"],
	forms: [new LoginForm(), new RegisterForm(), new LostPasswordForm()],
});
