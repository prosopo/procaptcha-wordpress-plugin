import { CommentForm } from "./classes/comment-form";
import { LoginForm } from "./classes/login-form";
import { RegisterForm } from "./classes/register-form";
import { LostPasswordForm } from "./classes/lost-password-form";
import { PasswordProtectionForm } from "./classes/password-protection-form";
import { IntegrationTest } from "@support/integration-test";

new IntegrationTest({
	forms: [
		new RegisterForm(),
		new LoginForm(),
		new LostPasswordForm(),
		new CommentForm({
			url: "/post-with-comments/",
		}),
		new PasswordProtectionForm({
			url: "/password-protected-post/",
		}),
	],
});
