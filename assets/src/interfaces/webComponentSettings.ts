import ComponentControllerInterface from "./componentControllerInterface";

interface WebComponentSettings {
	name: string;
	componentController: ComponentControllerInterface;
	processIfReconnected: boolean;
	waitWindowLoadedInsteadOfDomLoaded: boolean;
}

export default WebComponentSettings;
