import FormidableForms from "./formidable-forms";

class FormidableFormsWithoutAjax extends FormidableForms {
	protected toggleAjaxSubmissionSetting(isActivation: boolean): void {
		it("toggleAjaxSubmissionSetting", () => {
			this.integrationTest.login();

			cy.visit(
				"/wp-admin/admin.php?page=formidable&frm_action=settings&id=2",
			);

			let setting = cy.get('input[name="options[ajax_submit]"]');

			true === isActivation ? setting.check() : setting.uncheck();

			cy.get(".frm_submit_settings_btn").click();

			cy.get(".frm_updated_message").should("exist");
		});
	}

	protected beforeScenario() {
		super.beforeScenario();

		this.toggleAjaxSubmissionSetting(false);
	}

	protected afterScenario() {
		super.afterScenario();

		this.toggleAjaxSubmissionSetting(true);
	}
}

export default FormidableFormsWithoutAjax;
