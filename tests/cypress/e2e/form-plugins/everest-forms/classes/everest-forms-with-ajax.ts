import EverestForms from "./everest-forms";

class EverestFormsWithAjax extends EverestForms {
	protected toggleAjaxSubmissionSetting(isActivation: boolean): void {
		it("toggleAjaxSubmissionSetting", () => {
			this.integrationTest.login();

			cy.visit(
				"/wp-admin/admin.php?page=evf-builder&tab=settings&form_id=482",
			);

			let setting = cy.get(
				'input[name="settings[ajax_form_submission]"][type=checkbox]',
			);

			true === isActivation ? setting.check() : setting.uncheck();

			cy.get(".everest-forms-save-button").click();

			cy.get(".everest-forms-save-button").should(
				"not.have.class",
				"processing",
			);
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

export default EverestFormsWithAjax;
