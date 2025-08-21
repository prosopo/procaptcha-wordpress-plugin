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

			// if the 'settings' tab is not active, then the input is hidden,
			// so the "force" option is used to avoid the "Element is not visible" error.
			true === isActivation
				? setting.check({ force: true })
				: setting.uncheck({ force: true });

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
