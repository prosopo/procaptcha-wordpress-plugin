import GravityForms from "./gravity-forms";

class GravityFormsWithAjax extends GravityForms {
    protected toggleAjaxSubmissionSetting(isActivation: boolean): void {
        it("toggleAjaxSubmissionSetting", () => {
            this.integrationTest.login();

            cy.visit("/wp-admin/post.php?post=126&action=edit");

            const ajaxArgument = isActivation ? 'ajax="true"' : '';

            this.replaceShortcode('#blocks-shortcode-input-1', `[gravityform id="2" title="true" ${ajaxArgument}]`);
            this.replaceShortcode('#blocks-shortcode-input-3', `[gravityform id="1" title="true" ${ajaxArgument}]`);

            cy.get(".editor-post-publish-button").click();

            cy.get(".components-snackbar__content").should("exist");
        });
    }

    protected replaceShortcode(shortocodeSelector: string, shortcode: string): void {
        cy.get(shortocodeSelector).then(($input) => {
            cy.safeType($input, shortcode);
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
