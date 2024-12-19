import WpForms from "./wp-forms";

class WpFormsWithAjax extends WpForms {
	/**
	 * Using adding '.wpforms-ajax-form' class instead of the settings change in the UI,
	 * as their settings UI doesn't work inside the Cypress (they try to detect if they're inside iframe, and that line causes a fatal error).
	 */
	protected visitTargetPage(): void {
		super.visitTargetPage();

		cy.get("form.wpforms-form").each((form) => {
			cy.wrap(form).invoke("addClass", "wpforms-ajax-form");
		});
	}
}

export default WpFormsWithAjax;
