import { IntegrationTest } from "@support/integration-test";
import Jetpack from "./classes/jetpack";

// two forms on the same page just doesn't work (even with vanilla setup)...
const jetpackWithCaptcha = new Jetpack({
	expectedSubmissionsCount: 3,
});
jetpackWithCaptcha.setUrl("/jetpack-with-captcha/");
jetpackWithCaptcha.setFormWithoutCaptchaSelector("");

const jetpackWithoutCaptcha = new Jetpack({
	expectedSubmissionsCount: 1,
});
jetpackWithoutCaptcha.setUrl("/jetpack-without-captcha/");
jetpackWithoutCaptcha.setFormWithCaptchaSelector("");

new IntegrationTest({
	targetPluginSlugs: ["jetpack"],
	forms: [jetpackWithCaptcha, jetpackWithoutCaptcha],
});
