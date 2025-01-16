import {IntegrationTest} from "@support/integration-test";
import SmLoginForm from "./classes/sm-login-form";
import SmRegistrationForm from "./classes/sm-registration-form";
import SmLostPasswordForm from "./classes/sm-lost-password-form";

new IntegrationTest({
    targetPluginSlugs: ["simple-membership"],
    forms: [
        new SmLoginForm({
            url: "/membership-login/",
        }),
        new SmRegistrationForm({
            url: "/membership-registration/"
        }),
        new SmLostPasswordForm({
            url: "/membership-login/password-reset/",
        })
    ],
});
