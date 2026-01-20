import {IntegrationTest} from "@support/integration-test";
import {LoginForm} from "@wordpress/login-form";
import {RegisterForm} from "@wordpress/register-form";
import {LostPasswordForm} from "@wordpress/lost-password-form";

new IntegrationTest({
    targetPluginSlugs: [
        'change-wp-admin-login',
    ],
    loginUrl: '/login',
    forms: [
        new LoginForm({
            url: '/login',
        }),
        new RegisterForm({
            url: '/login/?action=register',
        }),
        new LostPasswordForm({
            url: '/login/?action=lostpassword',
        }),
    ],
});
