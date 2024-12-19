import { FormTest, Role } from "@support/form-test";

class ForumForm extends FormTest {
	protected getSubmitValues(userRole: string): object {
		let submitValues = super.getSubmitValues(userRole);

		// Field sets are differ depending on the user role.
		if (Role.USER === userRole) {
			delete submitValues["bbp_anonymous_name"];
			delete submitValues["bbp_anonymous_email"];
		}

		return submitValues;
	}

	protected toggleFeatureSupport(isActivation: boolean): void {
		it("enableFeatureSupport", () => {
			this.integrationTest.login();

			cy.visit("/wp-admin/post.php?post=47&action=edit");

			let input = cy.get(
				"input[name=prosopo_procaptcha_bbpress_forum_protection]",
			);

			true === isActivation ? input.check() : input.uncheck();

			cy.get("#publish").click();

			cy.get(".notice.notice-success").should("exist");
		});
	}
}

export { ForumForm };
