interface Settings {
	postType: string;
	countToRemove: number;
	onlyIfTotal?: number;
}

class RemovePosts {
	constructor(settings: Settings) {
		cy.login();

		cy.visit("/wp-admin/edit.php?post_type=" + settings.postType);

		let onlyIfTotal = settings.onlyIfTotal || 0;

		if (0 !== onlyIfTotal) {
			// Perform removals only if all topics were made successfully
			// (to avoid the default topic removal).
			cy.get(".submitdelete").should("have.length", settings.onlyIfTotal);
		}

		for (let i = 0; i < settings.countToRemove; i++) {
			// force, since the link is hidden before hovering.
			cy.get(".submitdelete").first().click({ force: true });
		}

		// Empty the trash.
		cy.get(".subsubsub .trash a").click();
		cy.get("#delete_all").click();
		cy.get(".notice.updated").should("exist");
	}
}

export { Settings, RemovePosts };
