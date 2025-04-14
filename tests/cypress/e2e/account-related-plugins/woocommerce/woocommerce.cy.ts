import { IntegrationTest } from "@support/integration-test";
import WooOrderTracking from "./classes/woo-order-tracking";
import WooLoginForm from "./classes/woo-login-form";
import WooLostPasswordForm from "./classes/woo-lost-password-form";
import WooRegisterForm from "./classes/woo-register-form";
import WooCheckoutClassic from "./classes/woo-checkout-classic";
import WooCheckoutBlocks from "./classes/woo-checkout-blocks";

new IntegrationTest({
	targetPluginSlugs: ["woocommerce"],
	forms: [
		// fixme
		/*new WooLoginForm(),
        new WooLostPasswordForm(),
        new WooRegisterForm(),
        new WooCheckoutClassic(),*/
		new WooCheckoutBlocks(),
		// new WooOrderTracking(),
	],
});
