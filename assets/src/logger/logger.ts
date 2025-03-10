import logLevel from "./logLevel.js";

interface Logger {
	log(level: logLevel, message: string, args?: object): void;

	debug(message: string, args?: object): void;

	warning(message: string, args?: object): void;
}

export default Logger;
