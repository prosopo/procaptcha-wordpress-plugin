import logLevel from "./logLevel.js";

interface ModuleLogger {
	log(module: string, level: logLevel, message: string, args?: object): void;

	debug(module: string, message: string, args?: object): void;

	warning(module: string, message: string, args?: object): void;
}

export default ModuleLogger;
