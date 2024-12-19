import { LoginForm } from "@wordpress/login-form";
import { RegisterForm } from "./classes/register-form";
import { IntegrationTest } from "@support/integration-test";
import { LostPasswordForm } from "./classes/lost-password-form";

new IntegrationTest({
	targetPluginSlugs: ["login-with-ajax"],
	forms: [new LoginForm(), new RegisterForm(), new LostPasswordForm()],
});
