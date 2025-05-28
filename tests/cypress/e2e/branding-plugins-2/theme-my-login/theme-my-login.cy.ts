import { LoginForm } from "./classes/login-form";
import { RegisterForm } from "./classes/register-form";
import { IntegrationTest } from "@support/integration-test";
import { LostPasswordForm } from "./classes/reset-password-form";

new IntegrationTest({
	targetPluginSlugs: ["theme-my-login"],
	loginUrl: "/theme-login",
	forms: [new LoginForm(), new RegisterForm(), new LostPasswordForm()],
});
