import LoggerInterface from "../interfaces/loggerInterface";
import ModuleLoggerInterface from "../interfaces/moduleLoggerInterface";
import Logger from "./logger";

class LoggerFactory {
	public makeLogger(
		module: string,
		moduleLogger: ModuleLoggerInterface,
	): LoggerInterface {
		return new Logger(module, moduleLogger, this.isDebugMode());
	}

	protected isDebugMode(): boolean {
		return (
			null !== window.localStorage.getItem("_wp_procaptcha_debug_mode")
		);
	}
}

export default LoggerFactory;
