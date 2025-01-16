import {LostPasswordForm} from "@wordpress/lost-password-form";

class SmLostPasswordForm extends LostPasswordForm {
    protected defineSettings() {
        super.defineSettings();

        this.isClientSideFieldValidationSupported = false;
        this.selectors = {
            formWithCaptcha: "#swpm-pw-reset-form",
            formWithoutCaptcha: "#swpm-pw-reset-form",
            successMessage: ".swpm-reset-pw-success",
            errorMessage: ".swpm-reset-pw-error",
            errorFieldMessage: "",
            captchaInput: "",
        };
        this.submitValues = {
            swpm_reset_email: "membership-for-reset-pass-test@wpengine.local",
        };
        this.messages = {
            success: "New password has been sent to your email address",
            fail: "Captcha validation failed",
        };
    }

    // the element is out of the form.
    protected getSuccessfulSubmitMessageSelector(formSelector: string, userRole: string): string {
        return this.selectors.successMessage;
    }

    // the element is out of the form.
    protected getFailSubmitMessageSelector(): string {
        return this.selectors.errorMessage;
    }
}

export default SmLostPasswordForm;
