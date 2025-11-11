import {IntegrationTest} from "@support/integration-test";
import WooOrderTracking from "./classes/woo-order-tracking";
import WooLoginForm from "./classes/woo-login-form";
import WooLostPasswordForm from "./classes/woo-lost-password-form";
import WooRegisterForm from "./classes/woo-register-form";

new IntegrationTest({
    targetPluginSlugs: ["woocommerce"],
    forms: [
        new WooLoginForm(),
        new WooLostPasswordForm(),
        new WooRegisterForm(),
        new WooOrderTracking(),
    ],
});
