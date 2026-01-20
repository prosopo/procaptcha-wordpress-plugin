import ModuleLogger from "./moduleLogger.js";
import PluginLogger from "./plugin/pluginLogger.js";
import type Logger from "./logger.js";

class LoggerFactory {
	public createLogger(module: string, moduleLogger: ModuleLogger): Logger {
		return new PluginLogger(module, moduleLogger, this.isDebugMode());
	}

	protected isDebugMode(): boolean {
		return (
			null !== window.localStorage.getItem("_wp_procaptcha_debug_mode")
		);
	}
}

export default LoggerFactory;
