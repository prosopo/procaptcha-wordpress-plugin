import { WebComponent } from "./webComponent.js";

interface WebComponentSettings {
	name: string;
	componentClass: WebComponent;
	processIfReconnected: boolean;
	waitWindowLoadedInsteadOfDomLoaded: boolean;
}

export { WebComponentSettings };
