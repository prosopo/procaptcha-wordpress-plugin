class Login {
	constructor(url: string) {
		this.tryToLogin(url);
	}

	protected submitLoginForm(url: string): void {
		cy.visit(url);

		cy.safeType("#user_login", "procaptcha");
		cy.safeType("#user_pass", "procaptcha");

		cy.get("form").then(($form) => {
			$form.append(
				'<input type="hidden" name="procaptcha-response" value="bypass">',
			);
		});

		cy.get("#wp-submit").focus().click();
	}

	// make up to 3 attempts to login, since for some reason the login form sometimes randomly fails to submit.
	protected tryToLogin(url: string, attempt_number: number = 1): void {
		cy.wrap(attempt_number).should("be.lessThan", 4);

		this.submitLoginForm(url);

		cy.url().then((currentUrl) => {
			if (false === currentUrl.includes(url)) {
				// make an assertion.
				cy.wrap(currentUrl).should("not.include", url);
				return;
			}

			cy.log("Login attempt failed, trying again.");

			attempt_number++;

			this.tryToLogin(url, attempt_number);
		});
	}
}

export default Login;
