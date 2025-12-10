import type Logger from "../logger.js";
import type ModuleLogger from "../moduleLogger.js";
import logLevel from "../logLevel.js";

class PluginLogger implements Logger {
	private readonly module: string;
	private readonly moduleLogger: ModuleLogger;
	private readonly isDebugMode: boolean;

	constructor(
		module: string,
		moduleLogger: ModuleLogger,
		isDebugMode: boolean,
	) {
		this.module = module;
		this.moduleLogger = moduleLogger;
		this.isDebugMode = isDebugMode;

		this.debug("Debug logging is enabled");
	}

	log(level: logLevel, message: string, args?: object): void {
		if (logLevel.DEBUG === level && false === this.isDebugMode) {
			return;
		}

		this.moduleLogger.log(this.module, level, message, args);
	}

	debug(message: string, args?: object): void {
		this.log(logLevel.DEBUG, message, args);
	}

	warning(message: string, args?: object): void {
		this.log(logLevel.WARNING, message, args);
	}
}

export default PluginLogger;
