import {LoginForm} from "@wordpress/login-form";

class SmLoginForm extends LoginForm {
    protected defineSettings() {
        super.defineSettings();

        this.isAuthSupportedByVendor = false;
        this.isClientSideFieldValidationSupported = true;
        this.selectors = {
            formWithCaptcha: "#swpm-login-form",
            formWithoutCaptcha: "#swpm-login-form",
            successMessage: "",
            errorMessage: ".swpm-login-error-msg",
            errorFieldMessage: "",
            captchaInput: "",
        };
        this.submitValues = {
            swpm_user_name: "member",
            swpm_password: "member",
        };
        this.messages = {
            success: "",
            fail: "Captcha validation failed on the login form.",
        };
    }
}

export default SmLoginForm;
