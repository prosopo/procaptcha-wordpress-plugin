import GravityForms from "./gravity-forms";

class GravityFormsWithAjax extends GravityForms {
	protected toggleAjaxSubmissionSetting(isActivation: boolean): void {
		it("toggleAjaxSubmissionSetting", () => {
			this.integrationTest.login();

			cy.visit("/wp-admin/post.php?post=126&action=edit");

			cy.get("#blocks-shortcode-input-0").then(($input) => {
				true === isActivation
					? cy.safeType(
							$input,
							'[gravityform id="2" title="true" ajax="true"]',
						)
					: cy.safeType($input, '[gravityform id="2" title="true"]');
			});

			cy.get(".editor-post-publish-button").click();

			cy.get(".components-snackbar__content").should("exist");
		});
	}

	protected beforeScenario() {
		super.beforeScenario();

		this.toggleAjaxSubmissionSetting(true);
	}

	protected afterScenario() {
		super.afterScenario();

		this.toggleAjaxSubmissionSetting(false);
	}
}

export default GravityFormsWithAjax;
