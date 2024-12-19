import LoggerInterface from "../interfaces/loggerInterface";
import ModuleLoggerInterface from "../interfaces/moduleLoggerInterface";
import logLevel from "../interfaces/logLevel";

class Logger implements LoggerInterface {
	private readonly module: string;
	private readonly moduleLogger: ModuleLoggerInterface;
	private readonly isDebugMode: boolean;

	constructor(
		module: string,
		moduleLogger: ModuleLoggerInterface,
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

export default Logger;
