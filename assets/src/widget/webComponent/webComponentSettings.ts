import ComponentControllerInterface from "../../interfaces/componentControllerInterface.js";

interface WebComponentSettings {
	name: string;
	componentController: ComponentControllerInterface;
	processIfReconnected: boolean;
	waitWindowLoadedInsteadOfDomLoaded: boolean;
}

export { WebComponentSettings };
