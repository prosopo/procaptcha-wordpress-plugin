interface Settings {
	countToRemove: number;
}

class RemoveUsers {
	constructor(settings: Settings) {
		cy.login();

		cy.visit("/wp-admin/users.php");

		// don't worry about potential removal of default user, as .submitdelete isn't showed for the current account.
		for (let i = 0; i < settings.countToRemove; i++) {
			cy.visit("/wp-admin/users.php");
			// force, since the link is hidden before hovering.
			cy.get(".submitdelete").first().click({ force: true });
			cy.get("#updateusers input[type=submit]").click();
			cy.get(".notice.updated").should("exist");
		}
	}
}

export { Settings, RemoveUsers };
