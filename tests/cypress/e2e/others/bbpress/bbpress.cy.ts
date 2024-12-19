import { LoginForm } from "./classes/login-form";
import { LostPasswordForm } from "./classes/lost-password-form";
import { NewTopicForm } from "./classes/new-topic-form";
import { RegisterForm } from "./classes/register-form";
import { ReplyForm } from "./classes/reply-form";
import { IntegrationTest } from "@support/integration-test";

new IntegrationTest({
	targetPluginSlugs: ["bbpress", "bbp-style-pack"],
	forms: [
		new RegisterForm(),
		new LoginForm(),
		new LostPasswordForm(),
		new NewTopicForm(),
		new ReplyForm(),
	],
});
