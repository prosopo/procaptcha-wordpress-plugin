import { IntegrationTest } from "@support/integration-test";
import Jetpack from "./classes/jetpack";

new IntegrationTest({
	targetPluginSlugs: ["jetpack"],
	forms: [new Jetpack()],
});
