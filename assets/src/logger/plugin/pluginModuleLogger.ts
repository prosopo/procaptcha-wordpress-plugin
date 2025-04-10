import logLevel from "#logger/logLevel.js";
import type ModuleLogger from "#logger/moduleLogger.js";

class PluginModuleLogger implements ModuleLogger {
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

export default PluginModuleLogger;
