import {RegisterForm} from "@wordpress/register-form";

class SmRegistrationForm extends RegisterForm {
    protected defineSettings() {
        super.defineSettings();

        this.isClientSideFieldValidationSupported = false;
        this.selectors = {
            formWithCaptcha: "#swpm-registration-form",
            formWithoutCaptcha: "#swpm-registration-form",
            successMessage: "",
            errorMessage: ".swpm_error",
            errorFieldMessage: "",
            captchaInput: "",
        };
        this.submitValues = {
            user_name: "new",
            email: "new@gmail.com",
            password: "password",
            password_re: "password",
            first_name: "new",
        };
        this.messages = {
            success: "",
            fail: "Security check: captcha validation failed.",
        };
    }

    // The message is out of the form
    protected getFailSubmitMessageSelector(): string {
        return this.selectors.errorMessage;
    }

    protected afterScenario() {
        super.afterScenario();

        it("removeAddedUsers", () => {
            cy.login();

            cy.visit("/wp-admin/admin.php?page=simple_wp_membership&member_action=bulk");

            cy.get("select[name=swpm_bulk_delete_account_level_of]").select("2");

            cy.get("input[name=swpm_bulk_delete_account_process]").click();

            cy.on('window:confirm', () => true);
        });
    }
}

export default SmRegistrationForm;
