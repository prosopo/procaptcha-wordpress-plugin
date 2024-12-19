import ModuleLoggerInterface from "../interfaces/moduleLoggerInterface";
import logLevel from "../interfaces/logLevel";

class ModuleLogger implements ModuleLoggerInterface {
	public log(
		module: string,
		level: logLevel,
		message: string,
		args: object = {},
	): void {
		console.log(`WP Procaptcha (${module}) [${level}]: ${message}`);

		if (0 !== Object.keys(args).length) {
			console.log(args);
		}
	}

	debug(module: string, message: string, args?: object) {
		this.log(module, logLevel.DEBUG, message, args);
	}

	warning(module: string, message: string, args?: object) {
		this.log(module, logLevel.WARNING, message, args);
	}
}

export default ModuleLogger;
